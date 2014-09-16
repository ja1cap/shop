<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Entity\Category;
use Shop\CatalogBundle\Entity\CategoryFilters;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface;
use Shop\ShippingBundle\Entity\ShippingLiftingPrice;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Weasty\Bundle\CatalogBundle\Category\CategoryInterface;

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

        $categoryFilters = null;
        $category = $this->get('shop_catalog.category.repository')->findOneBy(array(
            'slug' => $slug,
        ));

        if(!$category instanceof CategoryInterface){

            $categoryFilters = $this->getDoctrine()->getRepository('ShopCatalogBundle:CategoryFilters')->findOneBy(array(
                'slug' => $slug,
            ));

            if($categoryFilters instanceof CategoryFilters){
                $category = $categoryFilters->getCategory();
            } else {
                return $this->redirect($this->generateUrl('shop'));
            }

        }

        /**
         * @var \Shop\CatalogBundle\Filter\FiltersBuilder $filtersBuilder
         */
        $filtersBuilder = $this->get('shop_catalog.filters.builder');
        if($categoryFilters){
            $filtersResource = $filtersBuilder->buildFromCategoryFilters($categoryFilters);
        } else {
            $filtersResource = $filtersBuilder->buildFromRequest($category, null, $request);
        }

        $proposals = $this->getProposalRepository()->findProposalsByFilters($filtersResource);
        $proposalsCount = $this->getProposalRepository()->countProposals($filtersResource);

        $viewParameters = array(
            'category' => $category,
            'proposals' => $proposals,
            'proposalsCount' => $proposalsCount,
            'filtersResource' => $filtersResource,
        );

        switch($request->get('format')){
            case 'json':

                $tabsHtml = $this->renderView('ShopCatalogBundle:Default:category-tabs.html.twig', $viewParameters);
                $filtersHtml = $this->renderView('ShopCatalogBundle:Default:category-filters.html.twig', $viewParameters);
                $proposalsHtml = $this->renderView('ShopCatalogBundle:Default:category-proposals.html.twig', $viewParameters);

                $response = new JsonResponse(array(
                    'tabsHtml' => $tabsHtml,
                    'filtersHtml' => $filtersHtml,
                    'proposalsHtml' => $proposalsHtml,
                ));

                break;

            default:

                $response = $this->render('ShopCatalogBundle:Default:category.html.twig', $viewParameters);

        }

        $filtersBuilder->setFiltersCookies($category, $request, $response);

        return $response;

    }

    /**
     * @param $slug
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function proposalAction($slug, Request $request){

        $criteria = array();

        if(is_numeric($slug)){
            $criteria['id'] = (int)$slug;
        } else if(is_string($slug)) {
            $criteria['seoSlug'] = (string)$slug;
        }

        $proposal = $this->getProposalRepository()->findOneBy($criteria);

        if(!$proposal instanceof Proposal){
            throw $this->createNotFoundException('Товар не найден');
        }

        $category = $proposal->getCategory();

        $filtersBuilder = $this->get('shop_catalog.filters.builder');
        $filtersResource = $filtersBuilder->buildFromRequest($category, $proposal, $request);

        $priceData = $this->getProposalRepository()->findProposalPrice($filtersResource);
        var_dump($priceData);
        die;
        $price = $priceData ? $priceData['price'] : null;

        /**
         * @TODO add twig function for proposal features
         * @var $proposalFeatures \Weasty\Bundle\CatalogBundle\Feature\FeaturesResourceInterface
         */
        $proposalFeatures = $this->get('shop_catalog.proposal.features_builder')->build($category, $price);

        $shopCart = $this->buildShopCart($request);
        $shippingCalculatorResult = null;

        if($price instanceof ProposalPriceInterface){

            $customerCity = null;
            if($request->get('customerCity')){
                $customerCity = $this->get('weasty_geonames.city.repository')->findOneBy(array(
                    'id' => (int)$request->get('customerCity'),
                ));
            }
            $customerCity = $customerCity ?: $this->get('weasty_geonames.city.locator')->getCity();
            $customerLiftType = $request->get('customerLiftType', ShippingLiftingPrice::LIFT_TYPE_LIFT);
            $customerFloor = $request->get('customerFloor', 10);

            $shippingCalculatorResult = $this->get('shop_shipping.shipping_calculator')->calculate(array(
                'shopCartCategories' => array($category),
                'shopCartSummaryPrice' => $this->get('shop_catalog.price.currency.converter')->convert($price),
                'city' => $customerCity,
                'liftType' => $customerLiftType,
                'floor' => $customerFloor,
            ));

        }
        $priceData['shipping'] = $shippingCalculatorResult;

        $additionalCategoriesData = $this->getProposalAdditionalCategories($category, $request);


        /**
         * @var $actionRepository \Shop\DiscountBundle\Entity\ActionRepository
         */
        $actionRepository = $this->get('shop_discount.action.repository');
        $actions = $actionRepository->findActions($proposal->getId());

        $response = $this->render('ShopCatalogBundle:Default:proposal.html.twig', array(
            'category' => $category,
            'additionalCategoriesData' => $additionalCategoriesData,
            'proposal' => $proposal,
            'price' => $price,
            'priceData' => $priceData,
            'proposalFeatures' => $proposalFeatures,
            'actions' => $actions,
            'shopCart' => $shopCart,
            'filtersResource' => $filtersResource,
        ));
        $filtersBuilder->setFiltersCookies($category, $request, $response);

        return $response;

    }

    /**
     * @param Category $category
     * @param Request $request
     * @return array
     */
    protected function getProposalAdditionalCategories(Category $category, Request $request)
    {

        $additionalCategoriesData = array();

        /**
         * @var $additionalCategory Category
         */
        foreach ($category->getAdditionalCategories() as $additionalCategory) {

            $filtersBuilder = $this->get('shop_catalog.filters.builder');
            $filtersResource = $filtersBuilder->buildFromRequest($additionalCategory, null, $request);

            $additionalCategoryProposals = $this->getProposalRepository()->findProposalsByFilters($filtersResource, 1, 1);
            if ($additionalCategoryProposals) {

                $additionalCategoriesData[$additionalCategory->getId()] = array(
                    'category' => $additionalCategory,
                    'proposalData' => current($additionalCategoryProposals),
                );

            }

        }

        return $additionalCategoriesData;

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
        $storageData = json_decode($request->cookies->get('shopCart'), true);
        return $this->getShopCartFactory()->createShopCart($storageData);
    }

    /**
     * @return \Shop\CatalogBundle\Entity\ProposalRepository
     */
    protected function getProposalRepository(){
        return $this->get('shop_catalog.proposal.repository');
    }

}
