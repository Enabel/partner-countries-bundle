<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\DependencyInjection;

use Enabel\PartnerCountriesBundle\Controller\Admin\CountryCrudController;
use Enabel\PartnerCountriesBundle\Entity\Country;
use Enabel\PartnerCountriesBundle\Repository\CountryRepository;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('enabel_partner_countries');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('country_class')
            ->defaultValue(Country::class)
            ->validate()
            ->ifString()
            ->then(static function ($value): string {
                if (!class_exists($value) || !is_a($value, Country::class, true)) {
                    throw new InvalidConfigurationException(sprintf(
                        'Country class must be a valid class extending %s. "%s" given.',
                        Country::class,
                        $value
                    ));
                }

                return $value;
            })
            ->end()
            ->end()
            ->scalarNode('country_admin_controller')
            ->defaultValue(CountryCrudController::class)
            ->validate()
            ->ifString()
            ->then(static function ($value): string {
                if (!class_exists($value) || !is_a($value, CountryCrudController::class, true)) {
                    throw new InvalidConfigurationException(sprintf(
                        'Country admin controller must be a valid class extending %s. "%s" given.',
                        CountryCrudController::class,
                        $value
                    ));
                }

                return $value;
            })
            ->end()
            ->end()
            ->scalarNode('country_repository')
            ->defaultValue(CountryRepository::class)
            ->validate()
            ->ifString()
            ->then(static function ($value): string {
                if (!class_exists($value) || !is_a($value, CountryRepository::class, true)) {
                    throw new InvalidConfigurationException(sprintf(
                        'Country repository must be a valid class extending %s. "%s" given.',
                        CountryRepository::class,
                        $value
                    ));
                }

                return $value;
            })
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
