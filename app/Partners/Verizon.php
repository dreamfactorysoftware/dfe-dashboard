<?php
namespace DreamFactory\Enterprise\Dashboard\Partners;

use DreamFactory\Enterprise\Partner\AlertPartner;
use Illuminate\Http\Request;

class Verizon extends AlertPartner
{
    /**
     * Handle a partner event/request.
     *
     * @param array $request
     *
     * @return mixed
     */
    public function getPartnerResponse(Request $request)
    {
        try {
            $user = app('auth')->user();

            $data =
                [
                    'first_name'   => $user->first_name_text,
                    'last_name'    => $user->last_name_text,
                    'email'        => $user->email_addr_text,
                    'company_name' => $user->company_name_text,
                    'phone_number' => $user->phone_text,
                    'date'         => date('Y-m-d'),
                    'time'         => date('H:i:s')
                ]; // Build up user data for posting to UTC

            if (empty( $user->company_name_text ) === true) {
                $data['company_name'] = 'DreamFactory Software';
            }

            if (empty( $user->phone_text ) === true) {
                $data['phone_number'] = '';
            }

            $json = json_encode($data);

            $url = 'http://204.151.28.188:8081/rest/df_vzw_utcmysql/customer/'; // UTC URL

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'X-DreamFactory-Application-Name: DBAccess'
                )
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt(
                $ch,
                CURLOPT_USERPWD,
                "dev.vz@utcassociates.com:Password2"
            );

            $result = curl_exec($ch);
            $response = json_decode($result);
            curl_close($ch);

            $logMsg = date('Y-m-d H:i:s') . ' URL: ' . $url . ' payload: ' . $json . ' response: ' . $result . "\n";

            file_put_contents(storage_path() . "/logs/utc_post.log", $logMsg, FILE_APPEND);

            if (null !== ( $_redirect = $this->get('redirect-uri') )) {
                return \Redirect::to($_redirect);
            }

            \Log::debug('[partner.vz] no redirect for partner, going home.');
            \Redirect::to('/');
        } catch ( Exception $e ) {
            \Log::error('[partner.vz] exception calling UTC during partner redirect: ' . $e->getMessage());
            \Redirect::to('/');
        }
    }

    /*
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

            //  UTC url
            $_url = 'https://dsp-dev-vzw.cloud.dreamfactory.com/rest/db/customer?app_name=DBAccess';

            Curl::setUserName('dev.vzw@utcassociates.com');
            Curl::setPassword('Devp@ss2');

            $_response = Curl::post($_url, json_encode($_data));

            //  Log it
            file_put_contents(storage_path() . '/logs/utc_post.log',
                date('Y-m-d H:i:s') . 'URL: ' . $_url . ' payload: ' .
                print_r($_data, true) . ' response: ' . print_r($_response, true) . PHP_EOL, FILE_APPEND);

            //  Redirect
            if (null !== ($_redirect = $this->getPartnerDetail('redirect-uri'))) {
                \Log::debug('[partner.vz] redirect after utc post to ' . $_redirect);

                return \Redirect::to($_redirect);
            }

            \Log::debug('[partner.vz] no redirect for partner, going home.');
        } catch (\Exception $_ex) {
            \Log::error('[partner.vz] exception calling UTC during partner redirect: ' . $_ex->getMessage());
        }

        return \Redirect::to('/');
    }
     */

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
<div class="alert {$_context} alert-fixed partner-alert" role="alert" style="background-color: #ECEDEE; color: #333333; border-color: #ECEDEE">
{$_brand->getCopy($minimal)}
</div>
HTML;

        return str_ireplace('__CSRF_TOKEN__', csrf_token(), $_html);
    }
}
