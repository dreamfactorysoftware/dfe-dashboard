<?php namespace DreamFactory\Enterprise\Dashboard\Partners;

use DreamFactory\Enterprise\Partner\SitePartner;
use Illuminate\Http\Request;

class PoC extends SitePartner
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

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
        $_icon = '<img src="' . $_brand->getLogo(true) . '" class="partner-brand">';

        return <<<HTML
<div class="alert alert-fixed alert-dismissable well partner-well" role="alert">
    <button type="button" class="close" style="padding-right: 5px;" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h3>{$_icon} {$this->getPartnerDetail('name')} <small>{$_brand->getCopyright($minimal)}</small></h3>
    {$_brand->getCopy($minimal)}
</div>
HTML;
    }

    /** @inheritdoc */
    public function getPartnerResponse(Request $request)
    {
        //  Do stuff here
        //  Stop doing stuff here...

        //  either redirect somewhere else
        if (null !== ($_redirect = $this->get('redirect-uri'))) {
            return \Redirect::to($_redirect);
        }

        //  or redirect home
        return \Redirect::home();

        //  or return success in JSON
        //return SuccessPacket::make(['stuff' => 'value'], Response::HTTP_OK);

        //  or return error in JSON
        //return ErrorPacket::create(Response::HTTP_BAD_REQUEST, 'You did something naughty.');
    }

}