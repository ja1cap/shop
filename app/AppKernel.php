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
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Lsw\SecureControllerBundle\LswSecureControllerBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\jQueryBundle\SonatajQueryBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Sonata\MediaBundle\SonataMediaBundle(),
            new Sonata\EasyExtendsBundle\SonataEasyExtendsBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Application\Sonata\MediaBundle\ApplicationSonataMediaBundle(),
            new CoopTilleuls\Bundle\CKEditorSonataMediaBundle\CoopTilleulsCKEditorSonataMediaBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new SimpleThings\EntityAudit\SimpleThingsEntityAuditBundle(),
            new Shtumi\UsefulBundle\ShtumiUsefulBundle(),
            new JJs\Bundle\GeonamesBundle\JJsGeonamesBundle(),
            new Weasty\ResourceBundle\WeastyResourceBundle(),
            new Weasty\DoctrineBundle\WeastyDoctrineBundle(),
            new Weasty\GeonamesBundle\WeastyGeonamesBundle(),
            new Weasty\MoneyBundle\WeastyMoneyBundle(),
            new Shop\UserBundle\ShopUserBundle(),
            new Shop\MainBundle\ShopMainBundle(),
            new Shop\CatalogBundle\ShopCatalogBundle(),
            new Weasty\CatalogBundle\WeastyCatalogBundle(),
            new Shop\ShippingBundle\ShopShippingBundle(),
            new Shop\OrderManagementBundle\ShopOrderManagementBundle(),
            new Shop\AdminBundle\ShopAdminBundle(),
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
