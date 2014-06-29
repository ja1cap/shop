<?php

namespace Weasty\Bundle\CatalogBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('weasty_catalog');

        $rootNode
            ->children()
                ->arrayNode('category')
                    ->children()
                        ->arrayNode('entity')
                            ->children()
                                ->scalarNode('class')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('repository')
                            ->children()
                                ->scalarNode('class')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
