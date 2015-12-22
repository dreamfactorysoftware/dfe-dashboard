<?php namespace DreamFactory\Enterprise\Dashboard\Partners;

use DreamFactory\Enterprise\Partner\AlertPartner;
use Illuminate\Http\Request;

class Docomo extends AlertPartner
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Handle a partner event/request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function getPartnerResponse(Request $request)
    {
        return \Redirect::to($this->get('redirect-uri', '/'));
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
