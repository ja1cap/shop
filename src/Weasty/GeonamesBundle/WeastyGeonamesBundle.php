<?php

namespace Weasty\GeonamesBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Weasty\GeonamesBundle\DependencyInjection\Compiler\OverrideServiceCompilerPass;

/**
 * Class WeastyGeonamesBundle
 * @package Weasty\GeonamesBundle
 */
class WeastyGeonamesBundle extends Bundle
{

    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new OverrideServiceCompilerPass());
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return 'JJsGeonamesBundle';
    }

}
