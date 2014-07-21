<?php

namespace Weasty\Bundle\CatalogBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class WeastyCatalogExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('weasty_catalog.category.entity.class', $config['category']['entity']['class']);
        $container->setParameter('weasty_catalog.category.repository.class', $config['category']['repository']['class']);

        $container->setParameter('weasty_catalog.proposal.entity.class', $config['proposal']['entity']['class']);
        $container->setParameter('weasty_catalog.proposal.repository.class', $config['proposal']['repository']['class']);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('forms.xml');

    }
}
