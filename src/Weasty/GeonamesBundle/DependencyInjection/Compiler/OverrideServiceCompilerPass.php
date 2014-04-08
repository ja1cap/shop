<?php

namespace Weasty\GeonamesBundle\DependencyInjection\Compiler;

use JJs\Bundle\GeonamesBundle\Data\FeatureCodes;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class OverrideServiceCompilerPass
 * @package Weasty\GeonamesBundle\DependencyInjection\Compiler
 */
class OverrideServiceCompilerPass implements CompilerPassInterface {

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {

        $definition = $container->getDefinition('geonames.locality.importer');

        $definition->addMethodCall(
            'addLocalityRepository',
            array(
                new Reference('weasty.geonames.state.repository'),
                array(
                    FeatureCodes::ADM1,
                ),
            )
        );

        $definition->addMethodCall(
            'addLocalityRepository',
            array(
                new Reference('weasty.geonames.city.repository'),
                array(
                    FeatureCodes::PPL,
                    FeatureCodes::PPLA,
                    FeatureCodes::PPLA2,
                    FeatureCodes::PPLA3,
                    FeatureCodes::PPLA4,
                    FeatureCodes::PPLC,
                    FeatureCodes::PPLF,
                    FeatureCodes::PPLG,
                    FeatureCodes::PPLL,
                    FeatureCodes::PPLS,
                    FeatureCodes::PPLX,
                ),
            )
        );

        $definition = $container->getDefinition('geonames.country.loader');
        $definition
            ->setClass($container->getParameter('weasty.geonames.country.loader.class'))
            ->setArguments(array(
                new Reference('weasty.geonames.country.repository')
            ))
        ;

    }

}