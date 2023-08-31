<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle;

use Enabel\PartnerCountriesBundle\DependencyInjection\PartnerCountriesBundleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EnabelPartnerCountriesBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        return new PartnerCountriesBundleExtension();
    }
}
