<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Filter\CategoryFiltersBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @TODO create manufacturer proposals page
 * @TODO create proposals paginator
 * Class DefaultController
 * @package Shop\CatalogBundle\Controller
 */
class DefaultController extends Controller
{

    const CATALOG_FILTER_COOKIE_NAME = 'filterValues';

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function categoriesAction(){

        return $this->render('ShopCatalogBundle:Default:categories.html.twig');

    }

    /**
     * @param $slug
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function categoryAction($slug, Request $request){

        $category = $this->getDoctrine()->getRepository('ShopCatalogBundle:Category')->findOneBy(array(
            'slug' => $slug,
        ));

        if(!$category instanceof Category){
            return $this->redirect($this->generateUrl('shop'));
        }

        $filtersBuilder = $this->get('shop_catalog.category.filters.builder');
        $filtersResource = $filtersBuilder->buildFromRequest($category, null, $request);

        $proposals = $this->getProposalRepository()->findProposalsByParameters($category->getId(), $filtersResource);
        $shopCart = $this->buildShopCart($request);

        $viewParameters = array(
            'shopCart' => $shopCart,
            'category' => $category,
            'proposals' => $proposals,
            'filtersResource' => $filtersResource,
        );

        $response = $this->render('ShopCatalogBundle:Default:category.html.twig', $viewParameters);
        $filtersBuilder->setFiltersCookies($category, $request, $response);

        return $response;

    }

    /**
     * @return \Shop\CatalogBundle\Cart\ShopCartFactory
     */
    protected function getShopCartFactory()
    {
        return $this->get('shop_catalog.shop_cart.factory');
    }

    /**
     * @param Request $request
     * @return array
     * @throws \InvalidArgumentException
     */
    public function buildShopCart(Request $request)
    {
        $shopCartStorageData = json_decode($request->cookies->get('shopCart'), true);
        return $this->getShopCartFactory()->buildShopCart($shopCartStorageData);
    }

    /**
     * @return \Shop\CatalogBundle\Entity\ProposalRepository
     */
    protected function getProposalRepository(){
        return $this->getDoctrine()->getRepository('ShopCatalogBundle:Proposal');
    }

}
