<?php
namespace Weasty\Bundle\AdBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class AdminBannerController
 * @package Weasty\Bundle\AdBundle\Controller
 */
class AdminBannerController extends Controller {

    public function bannersAction()
    {
        $bannerRepository = $this->getBaseBannerRepository();
        return $this->render('WeastyAdBundle:AdminBanner:banners.html.twig', array(
            'entities' => $bannerRepository->getBanners(),
        ));
    }

    /**
     * @return \Weasty\Bundle\AdBundle\Entity\BaseBannerRepository
     */
    protected function getBaseBannerRepository(){
      return $this->get('weasty_ad.base.banner.repository');
    }

}