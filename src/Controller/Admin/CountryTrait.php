<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Menu\MenuItemInterface;
use Iterator;
use LogicException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

trait CountryTrait
{
    /**
     * @return Iterator<MenuItemInterface>
     */
    public function countryMenuEntry(): iterable
    {
        $parameterBag = $this->container->get('parameter_bag');
        if (!$parameterBag instanceof ParameterBagInterface) {
            throw new LogicException('The "parameter_bag" service is not available.');
        }

        $controllerFqcn = $parameterBag->get('enabel_partner_countries.country_admin_controller');
        if (!is_string($controllerFqcn) || !is_a($controllerFqcn, CountryCrudController::class, true)) {
            throw new LogicException(sprintf(
                'The "enabel_partner_countries.country_admin_controller" parameter must be a class extending "%s".',
                CountryCrudController::class
            ));
        }

        yield MenuItem::linkTo(
            $controllerFqcn,
            'enabel_partner_countries.admin.menu.country',
            'fa fa-earth-africa'
        );
    }
}
