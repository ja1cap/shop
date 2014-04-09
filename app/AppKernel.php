<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{

    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sylius\Bundle\ResourceBundle\SyliusResourceBundle(),
            new \Sylius\Bundle\MoneyBundle\SyliusMoneyBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Lsw\SecureControllerBundle\LswSecureControllerBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new SimpleThings\EntityAudit\SimpleThingsEntityAuditBundle(),
            new Shop\OrderManagementBundle\ShopOrderManagementBundle(),
            new JJs\Bundle\GeonamesBundle\JJsGeonamesBundle(),
            new Weasty\GeonamesBundle\WeastyGeonamesBundle(),
            new Weasty\DoctrineBundle\WeastyDoctrineBundle(),
            new Shop\UserBundle\ShopUserBundle(),
            new Shop\MainBundle\ShopMainBundle(),
            new Shop\CatalogBundle\ShopCatalogBundle(),
            new Shop\ShippingBundle\ShopShippingBundle(),
            new Weasty\ResourceBundle\WeastyResourceBundle(),
            new Weasty\MoneyBundle\WeastyMoneyBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Acme\DemoBundle\AcmeDemoBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
