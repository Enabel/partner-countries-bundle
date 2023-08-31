<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Tests;

use Enabel\PartnerCountriesBundle\EnabelPartnerCountriesBundle;
use PHPUnit\Framework\TestCase;

class EnabelPartnerCountriesBundleTest extends TestCase
{
    public function testGetPath(): void
    {
        $this->assertSame(\dirname(__DIR__), (new EnabelPartnerCountriesBundle())->getPath());
    }
}
