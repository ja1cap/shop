<?php
namespace Weasty\Bundle\AdBundle\Twig;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * Class BannerExtension
 * @package Weasty\Bundle\AdBundle\Twig
 */
class BannerExtension extends \Twig_Extension {

    /**
     * @var \Weasty\Bundle\AdBundle\Entity\BaseBannerRepository
     */
    protected $bannersRepository;

    /**
     * @param \Doctrine\Common\Persistence\ObjectRepository $bannersRepository
     */
    function __construct(ObjectRepository $bannersRepository)
    {
        $this->bannersRepository = $bannersRepository;
    }

    /**
     * @return array
     */
    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction('weasty_ad_banners', array($this, 'getBanners')),
        );
    }

    /**
     * @return \Weasty\Bundle\AdBundle\Banner\BannerInterface[]
     */
    public function getBanners(){
        return $this->bannersRepository->getBanners();
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'weasty_ad_banner';
    }

}