<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Tests\Utils;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Enabel\PartnerCountriesBundle\Controller\Admin\CountryTrait;

class DashboardController extends AbstractDashboardController
{
    use CountryTrait;
}
