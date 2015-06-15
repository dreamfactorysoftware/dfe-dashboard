<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers;

use DreamFactory\Enterprise\Common\Http\Controllers\BaseController;
use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;
use DreamFactory\Enterprise\Dashboard\Enums\PanelTypes;
use DreamFactory\Enterprise\Dashboard\Facades\Dashboard;
use DreamFactory\Enterprise\Dashboard\Partners\PoC;
use DreamFactory\Enterprise\Database\Models\Snapshot;
use DreamFactory\Enterprise\Database\Models\User;
use DreamFactory\Enterprise\Partner\Facades\Partner;
use DreamFactory\Library\Utility\Inflector;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function __construct(Request $request)
    {
        //  require auth'd users
        $this->middleware('auth');
    }

    /**
     * @param Request    $request
     * @param string|int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function status(Request $request, $id)
    {
        $_status = Dashboard::handleRequest($request, $id);

        return response()->json($_status);
    }

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function logout(Request $request)
    {
        !\Auth::guest() && \Auth::logout();
        \Redirect::to('auth/login');
    }

    /**
     * @param Request $request
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function control(Request $request, $id = null)
    {
        $this->_validateCaptcha($request);

        $_response = Dashboard::handleRequest($request, $id);

        if (true === $_response) {
            \Session::flash('dashboard-success', 'Your request completed successfully.');
        } else if (false === $_response) {
            \Session::flash('dashboard-failure', 'There was a problem with your request.');
        }

        return \Redirect::action('HomeController@index');
    }

    /**
     * Show the application dashboard to the user.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $_message = isset($messages) ? $messages : null;
        $_defaultDomain = '.' . trim(config('dfe.dashboard.default-domain'), '. ');

        /** @type User $_user */
        $_user = \Auth::user();

        $_coreData = [
            /** General */
            'panelContext'        => config('dfe.panels.default.context', DashboardDefaults::PANEL_CONTEXT),
            'panelType'           => PanelTypes::SINGLE,
            'defaultDomain'       => $_defaultDomain,
            'message'             => $_message,
            'isAdmin'             => $_user->admin_ind,
            'displayName'         => $_user->nickname_text,
            'defaultInstanceName' =>
                (1 != $_user->admin_ind
                    ? config('dfe.instance-prefix')
                    : null
                ) . Inflector::neutralize(str_replace(' ', '-', \Auth::user()->nickname_text)),
        ];

        $_create = Dashboard::renderPanel('create',
            array_merge($_coreData, ['instanceName' => PanelTypes::CREATE, 'panelType' => PanelTypes::CREATE]));

        $_import =
            Dashboard::renderPanel(
                'import',
                array_merge(
                    $_coreData,
                    [
                        'snapshotList' => $this->_getSnapshotList(),
                        'instanceName' => PanelTypes::IMPORT,
                        'panelType'    => PanelTypes::IMPORT,
                    ]
                )
            );

        $_instances = Dashboard::userInstanceTable(null, true);

        //  The name of the site partner, if any.
        if (null !== ($_partnerId = config('dfe.partner'))) {
            Partner::register($_partnerId, new PoC($_partnerId, config('partner.' . $_partnerId)));
        }

        return view(
            'app.home',
            array_merge(
                $_coreData,
                [
                    /** The instance create panel */
                    'instanceCreator'  => $_create,
                    /** The instance import panel */
                    'snapshotImporter' => $_import,
                    /** The instance list */
                    'instances'        => $_instances,
                    'partner'          => $_partnerId ? Partner::resolve($_partnerId) : null,
                ]
            )
        );
    }

    /**
     * @return array A list of available snapshots for this user
     */
    protected function _getSnapshotList()
    {
        $_result = [];
        $_rows = Snapshot::where('user_id', \Auth::user()->id)->get();

        if (!empty($_rows)) {
            /** @var Snapshot[] $_rows */
            foreach ($_rows as $_row) {
                $_result[] = [
                    'id'   => $_row->id,
                    'name' => $_row->snapshot_id_text,
                ];
            }
        }

        return $_result;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function _validateCaptcha($request)
    {
        //  If captcha is on, check it...
        if (config('dfe.dashboard.require-captcha') && $request->isMethod(Request::METHOD_POST)) {
            $_validator = \Validator::make(
                \Input::all(),
                [
                    'g-recaptcha-response' => 'required|recaptcha',
                ]
            );

            if (!$_validator->passes()) {
                \Log::error('recaptcha failure: ' . print_r($_validator->errors()->all(), true));
                \Session::flash('dashboard-failure', 'There was a problem with your request.');

                return false;
            }
        }

        return true;
    }
}
