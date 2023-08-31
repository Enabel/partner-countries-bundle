<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use Iterator;

trait CountryTrait
{
    /**
     * @return Iterator<MenuItemInterface>
     */
    public function countryMenuEntry(): iterable
    {
        $parameterBag = $this->container->get('parameter_bag');

        yield MenuItem::linkToCrud(
            'enabel_partner_countries.admin.menu.country',
            'fa fa-earth-africa',
            $parameterBag->get('enabel_partner_countries.country_class')
        );
    }
}
