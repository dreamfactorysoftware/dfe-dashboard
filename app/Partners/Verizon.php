<?php
namespace DreamFactory\Enterprise\Dashboard\Partners;

use DreamFactory\Enterprise\Partner\AlertPartner;
use DreamFactory\Library\Utility\Curl;
use Illuminate\Http\Request;

class Verizon extends AlertPartner
{
    /**
     * Handle a partner event/request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getPartnerResponse(Request $request)
    {
        try {
            $_user = \Auth::user();

            // Build up user data for posting to UTC
            $_data = [
                'record' => [
                    'first_name'   => $_user->first_name_text,
                    'last_name'    => $_user->last_name_text,
                    'email'        => $_user->email_addr_text,
                    'company_name' => $_user->company_name_text ?: 'DreamFactory Software, Inc.',
                    'phone_number' => $_user->phone_text ?: '',
                ],
            ];

            $_url = 'https://dsp-dev-vzw.cloud.dreamfactory.com/rest/db/customer'; // UTC URL

            $_response = Curl::post($_url, $_data,
                [
                    CURLOPT_USERPWD    => 'dev.vzw@utcassociates.com:Devp@ss2',
                    CURLOPT_HTTPHEADER => ['X-DreamFactory-Application-Name: DBAccess',],
                ]
            );

            //  Log it
            file_put_contents(storage_path() . '/logs/utc_post.log',
                date('Y-m-d H:i:s') . 'URL: ' . $_url . ' payload: ' .
                print_r($_data, true) . ' response: ' . print_r($_response, true) . PHP_EOL, FILE_APPEND);

            //  Redirect
            if (null !== ($_redirect = $this->getPartnerDetail('redirect-uri'))) {
                \Log::debug('[partner.vz] redirect after utc post to ' . $_redirect);
                \Redirect::to($_redirect);
            }
        } catch (\Exception $_ex) {
            \Log::error('[partner.vz] exception calling UTC during partner redirect: ' . $_ex->getMessage());
        }

        \Log::debug('[partner.vz] no redirect, going home.');

        \Redirect::to('/');
    }

    /**
     * Get the partner's content for placement
     *
     * @param bool $minimal True if minimal content is requested
     *
     * @return string
     */
    public function getWebsiteContent($minimal = false)
    {
        $_brand = $this->getPartnerBrand();
        $_context = $this->getPartnerDetail('alert-context');

        //<button type="button" class="close" style="padding-right: 5px;" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>

        $_html = <<<HTML
<div class="alert {$_context} alert-fixed partner-alert" role="alert">
{$_brand->getCopy($minimal)}
</div>
HTML;

        return str_ireplace('__CSRF_TOKEN__', csrf_token(), $_html);
    }
}
