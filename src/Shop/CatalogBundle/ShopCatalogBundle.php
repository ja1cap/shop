<?php

namespace Shop\CatalogBundle;

use Shop\MainBundle\Entity\Settings;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ShopCatalogBundle
 * @package Shop\CatalogBundle
 */
class ShopCatalogBundle extends Bundle
{

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {

        parent::setContainer($container);

        /**
         * @var $doctrine \Doctrine\Bundle\DoctrineBundle\Registry
         */
        $doctrine = $container->get('doctrine');
        $settings = $doctrine->getRepository('ShopMainBundle:Settings')->findOneBy(array());

        if(!$settings){
            $settings = new Settings();
        }

        $container->get('twig')->addGlobal('settings', $settings);

    }

}
