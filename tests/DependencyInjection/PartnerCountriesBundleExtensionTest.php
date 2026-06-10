<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\Tests\DependencyInjection;

use Enabel\PartnerCountriesBundle\DependencyInjection\PartnerCountriesBundleExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class PartnerCountriesBundleExtensionTest extends TestCase
{
    protected ?ContainerBuilder $configuration;

    protected function tearDown(): void
    {
        $this->configuration = null;
    }

    public function testThrowExceptionUnlessCountryModelClassSet(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new PartnerCountriesBundleExtension();
        $config = $this->getEmptyConfig();
        if (is_array($config)) {
            unset($config['country_class']);
        }
        $loader->load(['enabel_partner_countries' => $config], new ContainerBuilder());
    }

    public function testThrowExceptionUnlessCountryRepositoryClassSet(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new PartnerCountriesBundleExtension();
        $config = $this->getEmptyConfig();
        if (is_array($config)) {
            unset($config['country_repository']);
        }
        $loader->load(['enabel_partner_countries' => $config], new ContainerBuilder());
    }

    public function testThrowExceptionOnInvalidAdminControllerClass(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $loader = new PartnerCountriesBundleExtension();
        $config = $this->getValidConfig();
        if (is_array($config)) {
            $config['country_admin_controller'] = 'Acme\MyBundle\Controller\Admin\CountryCrudController';
        }
        $loader->load(['enabel_partner_countries' => $config], new ContainerBuilder());
    }

    public function testAdminControllerClassWithDefaults(): void
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(
            'Enabel\PartnerCountriesBundle\Controller\Admin\CountryCrudController',
            'enabel_partner_countries.country_admin_controller'
        );
    }

    public function testModelClassWithDefaults(): void
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(
            'Enabel\PartnerCountriesBundle\Tests\Utils\CountryModel',
            'enabel_partner_countries.country_class'
        );
    }

    public function testRepositoryClassWithDefaults(): void
    {
        $this->createEmptyConfiguration();

        $this->assertParameter(
            'Enabel\PartnerCountriesBundle\Tests\Utils\CountryModelRepository',
            'enabel_partner_countries.country_repository'
        );
    }

    /**
     * @return mixed|array<string, mixed>
     */
    protected function getEmptyConfig(): mixed
    {
        $yaml = <<<EOF
country_class: Acme\MyBundle\Document\Country
country_repository: Acme\MyBundle\Repository\CountryRepository
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * @return mixed|array<string, mixed>
     */
    protected function getValidConfig(): mixed
    {
        $yaml = <<<EOF
country_class: Enabel\PartnerCountriesBundle\Tests\Utils\CountryModel
country_repository: Enabel\PartnerCountriesBundle\Tests\Utils\CountryModelRepository
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    protected function createEmptyConfiguration(): void
    {
        $this->configuration = new ContainerBuilder();
        $loader = new PartnerCountriesBundleExtension();
        $config = $this->getValidConfig();
        $loader->load(['enabel_partner_countries' => $config], $this->configuration);
    }

    /**
     * @param mixed  $value
     */
    private function assertParameter($value, string $key): void
    {
        $parameter = null;
        if ($this->configuration instanceof ContainerBuilder) {
            $parameter = $this->configuration->getParameter($key);
        }

        $this->assertSame($value, $parameter, sprintf('%s parameter is correct', $key));
    }
}
