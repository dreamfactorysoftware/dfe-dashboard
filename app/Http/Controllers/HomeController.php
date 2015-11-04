<?php namespace DreamFactory\Enterprise\Dashboard\Http\Controllers;

use Carbon\Carbon;
use DreamFactory\Enterprise\Common\Http\Controllers\BaseController;
use DreamFactory\Enterprise\Common\Traits\EntityLookup;
use DreamFactory\Enterprise\Dashboard\Enums\DashboardDefaults;
use DreamFactory\Enterprise\Dashboard\Enums\PanelTypes;
use DreamFactory\Enterprise\Dashboard\Facades\Dashboard;
use DreamFactory\Enterprise\Database\Models\RouteHash;
use DreamFactory\Enterprise\Database\Models\Snapshot;
use DreamFactory\Enterprise\Database\Models\User;
use DreamFactory\Enterprise\Partner\Contracts\WebsitePartner;
use DreamFactory\Enterprise\Partner\Facades\Partner;
use DreamFactory\Library\Utility\Curl;
use DreamFactory\Library\Utility\Flasher;
use DreamFactory\Library\Utility\Inflector;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\Flysystem\Filesystem;

class HomeController extends BaseController
{
    //******************************************************************************
    //* Constants
    //******************************************************************************

    /**
     * @type int
     */
    const MAX_LOOKUP_RETRIES = 5;

    //******************************************************************************
    //* Traits
    //******************************************************************************

    use EntityLookup;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function __construct(Request $request)
    {
        //  require auth'd users
        $this->middleware('auth');

        if (\Auth::guest() && null !== ($_subGuid = $request->input('submissionGuid'))) {
            //  Make sure the request is from hubspot...
            if (false !== stripos($_ref = $request->get('http-referrer'), 'info.dreamfactory.com')) {
                \Log::notice('bogus referrer on inbound from landing page: ' . $_ref);
            }

            $this->autoLoginRegistrant($_subGuid, $request->input('pem'));
        }
    }

    /**
     * Request a download of a snapshot by id/hash
     *
     * @param string $snapshotId The snapshot-id
     */
    public function download($snapshotId)
    {
        try{
            /** @type RouteHash $_hash */
            $_hash = RouteHash::with(['snapshot'])->byHash($snapshotId)->firstOrFail();
            /** @type Filesystem $_fs */
            $_fs = $_hash->snapshot->instance->getSnapshotMount();
            $_fs->readStream($_hash->actual_path_text);
        }catch (\Exception $_ex){
            abort(Response::HTTP_NOT_FOUND);
        }
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

        /** @noinspection PhpUndefinedMethodInspection */

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
     * @param Request    $request
     * @param string     $id
     * @param mixed|null $extra
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function control(Request $request, $id = null, $extra = null)
    {
        $this->_validateCaptcha($request);

        Dashboard::handleRequest($request, $id, $extra);

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
        $_defaultDomain = '.' . trim(config('dashboard.default-domain'), '. ');

        /** @type User $_user */
        $_user = \Auth::user();

        /** @noinspection PhpUndefinedFieldInspection */
        $_coreData = [
            /** General */
            'panelContext'        => config('panels.default.context', DashboardDefaults::PANEL_CONTEXT),
            'panelType'           => PanelTypes::SINGLE,
            'defaultDomain'       => $_defaultDomain,
            'message'             => $_message,
            'isAdmin'             => $_user->admin_ind,
            'displayName'         => $_user->nickname_text,
            'defaultInstanceName' => (1 != $_user->admin_ind ? config('dfe.instance-prefix') : null) .
                Inflector::neutralize(str_replace(' ', '-', \Auth::user()->nickname_text)),
        ];

        $_create = Dashboard::renderPanel('create',
            array_merge($_coreData,
                [
                    'instanceName' => PanelTypes::CREATE,
                    'panelType'    => PanelTypes::CREATE,
                    'importables'  => $this->getUserImportables(),
                ]));

        $_instances = Dashboard::userInstanceTable(null, true);

        /** @type WebsitePartner $_partner */
        //  The name of the site partner, if any.
        $_partner = null;

        if (!empty($_partnerId = config('dfe.partner'))) {
            $_partner = Partner::resolve($_partnerId);
        }

        return view('app.home',
            array_merge($_coreData,
                [
                    /** The instance create panel */
                    'instanceCreator' => $_create,
                    /** The instance list */
                    'instances'       => $_instances,
                    /** Partner junk */
                    'partner'         => $_partner ?: null,
                    'partnerContent'  => $_partner ? $_partner->getWebsiteContent() : null,
                ]));
    }

    /**
     * @return array A list of available exports for this user
     */
    protected function getUserImportables()
    {
        $_result = [];
        /** @noinspection PhpUndefinedFieldInspection */
        $_rows = Snapshot::byUserId(\Auth::user()->id)->orderBy('create_date', 'desc')->get([
            'id',
            'instance_id',
            'snapshot_id_text',
        ]);

        if (!empty($_rows)) {
            /** @var Snapshot[] $_rows */
            foreach ($_rows as $_row) {
                list($_date, $_instanceName) = explode('.', $_row->snapshot_id_text, 2);

                try{
                    //  Find instance, dead or alive!
                    $_instance = $this->_locateInstance($_instanceName);

                    $_result[] = [
                        'id'            => $_row->id,
                        'name'          => $_row->snapshot_id_text,
                        'instance-id'   => $_instance->instance_id_text,
                        'export-date'   => Carbon::create($_row->create_date)->toFormattedDateString(),
                        'instance-name' => $_instanceName,
                    ];
                }catch (ModelNotFoundException $_ex){
                    //  ignored on purpose
                }
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
        if (config('dashboard.require-captcha') && $request->isMethod(Request::METHOD_POST)) {
            $_validator = \Validator::make(\Input::all(),
                [
                    'g-recaptcha-response' => 'required|recaptcha',
                ]);

            if (!$_validator->passes()) {
                \Log::error('recaptcha failure: ' . print_r($_validator->errors()->all(), true));
                Flasher::set('There was a problem with your request.', false);

                return false;
            }
        }

        return true;
    }

    /**
     * Given a HubSpot "submissionGuid", locate a registrant with the same conversion-id
     *
     * @param string      $subGuid      The form submission GUID
     * @param string|null $partnerEmail Partner registrant email address
     *
     * @return bool Returns false if nothing found otherwise logs user in and redirects to home page
     */
    protected function autoLoginRegistrant($subGuid, $partnerEmail = null)
    {
        for ($_i = 0; $_i < static::MAX_LOOKUP_RETRIES; $_i++) {
            if ($this->locateContactBySubmissionGuid($subGuid, $partnerEmail) || null != $partnerEmail) {
                break;
            }

            //  Take a nap
            sleep(2);
        }
    }

    /**
     * @param string      $subGuid
     * @param string|null $partnerEmail Partner registrant email address
     *
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    protected function locateContactBySubmissionGuid($subGuid, $partnerEmail)
    {
        if ('false' !== $subGuid && empty($partnerEmail)) {
            $_url =
                'https://api.hubapi.com/contacts/v1/lists/recently_updated/contacts/recent/?hapikey=' .
                config('marketing.hubspot.api-key') .
                '&count=50';

            if (false === ($_response = Curl::get($_url))) {
                \Log::debug('[auth.landing-page] recent contact pull failed.');

                return false;
            }

            if (empty($_response) ||
                !($_response instanceof \stdClass) ||
                !isset($_response->contacts) ||
                empty($_response->contacts)
            ) {
                //  Methinks thine guid is bogus
                \Log::debug('[auth.landing-page] recent contacts empty or invalid.');
                \Log::debug('[auth.landing-page] * response: ' . print_r($_response, true));

                return false;
            }

            //  Mine for gold...
            $_email = null;

            /**
             * GHA 2015-06-16
             * This has to be the most ridiculous way to get a contact's email address that I've ever seen.
             */
            foreach ($_response->contacts as $_contact) {
                if (isset($_contact->{'form-submissions'})) {
                    foreach ($_contact->{'form-submissions'} as $_sub) {
                        if (isset($_sub->{'conversion-id'}) && $subGuid == $_sub->{'conversion-id'}) {
                            if (isset($_contact->{'identity-profiles'})) {
                                foreach ($_contact->{'identity-profiles'} as $_profile) {
                                    if (isset($_profile->identities)) {
                                        foreach ($_profile->identities as $_identity) {
                                            if (isset($_identity->type) &&
                                                'EMAIL' == $_identity->type &&
                                                isset($_identity->value)
                                            ) {
                                                $_email = $_identity->value;
                                                break 4;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            //  Didn't find this person out of the last 50? how could he just have been redirected??
            if (empty($_email)) {
                \Log::debug('[auth.landing-page] subGuid "' . $subGuid . '" not found in recents');

                return false;
            }

            \Log::debug('[auth.landing-page] subGuid "' . $subGuid . '" attached with email "' . $_email . '"');
        }else {
            //  Make sure it came from our domain...
            if (null === ($_referrer = \Request::server('HTTP_REFERER')) ||
                false === stripos($_referrer, 'verizon.dreamfactory.com')
            ) {
                \Log::debug('[auth.landing-page] bad referrer "' . $_referrer . '" in auto-login request.');

                return false;
            }

            $_email = $partnerEmail;
            \Log::debug('[auth.landing-page] using partner supplied email "' . $_email . '"');
        }

        //  Lookup email address
        try{
            $_user = User::byEmail($_email)->firstOrFail();
            \Log::debug('[auth.landing-page] subGuid "' . $subGuid . '"/"' . $_email . '" user id#' . $_user->id);
        }catch (ModelNotFoundException $_ex){
            \Log::debug('[auth.landing-page] subGuid "' . $subGuid . '"/"' . $_email . '" no related user.');

            return false;
        }

        //  Ok, now we have a user, we need to log his ass in...
        /** @noinspection PhpParamsInspection */
        \Auth::login($_user);

        \Log::info('[auth.landing-page] auto-login user "' . $_email . '"');

        \Redirect::to('/auth/login');

        return true;
    }
}
