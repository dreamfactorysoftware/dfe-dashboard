<?php namespace DreamFactory\Enterprise\Dashboard\Partners;

use DreamFactory\Enterprise\Partner\AlertPartner;
use Illuminate\Http\Request;

class DefaultPartner extends AlertPartner
{
    /**
     * Get the partner's content for placement
     *
     * @param bool $minimal True if minimal content is requested
     *
     * @return string
     */
    public function getWebsiteContent( $minimal = false )
    {
        $_brand = $this->getPartnerBrand();
        $_context = $this->getPartnerDetail( 'alert-context' );
        $_logo = $_brand->getLogo();

        if (!is_null($_logo)) {
        $_html = '<div class="alert ' . $_context . ' alert-fixed partner-alert" role="alert" style="background-color: #FFFFFF; color: #333333; border-color: #FFFFFF">
    <div class="row partner-row">
        <div style="padding-left: 0;"><img src="' . $_logo . '" class="partner-brand"></div>
    </div>
</div>';
        } else {
            $_html = '';
        }

        return str_ireplace( '__CSRF_TOKEN__', csrf_token(), $_html );
    }
}