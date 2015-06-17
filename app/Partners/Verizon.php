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
     * @param array $request
     *
     * @return mixed
     */
    public function getPartnerResponse(Request $request)
    {
        $user = app('auth')->user();

        $data =
            [
                'first_name' => $user->first_name_text,
                'last_name' => $user->last_name_text,
                'email' => $user->email_addr_text,
                'company_name' => $user->company_name_text,
                'phone_number' => $user->phone_text
            ]; // Build up user data for posting to UTC

        if (empty($user->company_name_text) === true) {
            $data['company_name'] = 'DreamFactory Software';
        }

        if (empty($user->phone_text) === true) {
            $data['phone_number'] = '';
        }

        //$json = json_encode($data);

        $url = 'https://dsp-dev-vzw.cloud.dreamfactory.com/rest/db/customer'; // UTC URL

        $response = Curl::post($url, $data,
            [
                CURLOPT_USERPWD => 'dev.vzw@utcassociates.com:Devp@ss2',
                CURLOPT_HTTPHEADER => ['X-DreamFactory-Application-Name: DBAccess'
                ]
            ]
        );

//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt(
//            $ch,
//            CURLOPT_HTTPHEADER,
//            array(
//                'X-DreamFactory-Application-Name: DBAccess'
//            )
//        );
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_USERPWD, "dev.vzw@utcassociates.com:Devp@ss2");
//
//        $result = curl_exec($ch);
//        $response = json_decode($result);
//        curl_close($ch);

        $logMsg = date('Y-m-d H:i:s') . 'URL: ' . $url . ' payload: ' . print_r($data, true) . ' response: ' . print_r($response, true) . "\n";

        file_put_contents(storage_path() . "/logs/utc_post.log", $logMsg, FILE_APPEND);

        if (null !== ($_redirect = $this->get('redirect-uri'))) {
            \Redirect::to($_redirect);
        }
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
