<?php namespace DreamFactory\Enterprise\Dashboard\Partners;

use DreamFactory\Enterprise\Partner\SitePartner;

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

        return <<<HTML
<div class="row">
    <div class="well">
    <h3>{$_brand->getLogo(true)} {$this->getPartnerDetail('name')}</h3>
    <p>{$_brand->getCopy($minimal)}</p>
    <p>{$_brand->getCopyright($minimal)}</p>
</div>
</div>
HTML;
    }
}