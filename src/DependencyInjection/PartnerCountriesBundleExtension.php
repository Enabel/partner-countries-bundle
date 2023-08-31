<?php

declare(strict_types=1);

namespace Enabel\PartnerCountriesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PartnerCountriesBundleExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param array<string, mixed> $configs
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config as $key => $value) {
            $container->setParameter('enabel_partner_countries.' . $key, $value);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(dirname(__DIR__, 2) . '/config'));
        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs = $container->getExtensionConfig($this->getAlias());
        /** @var ConfigurationInterface $configuration */
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);
        $container->prependExtensionConfig('enabel_partner_countries', $config);

        $twigConfig['globals']['enabel_partner_countries'] = [];
        foreach ($config as $k => $v) {
            $twigConfig['globals']['enabel_partner_countries'][$k] = $v;
        }

        $container->prependExtensionConfig('twig', $twigConfig);
    }

    public function getAlias(): string
    {
        return 'enabel_partner_countries';
    }
}
