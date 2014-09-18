<?php

namespace Shop\CatalogBundle\Controller;

use Shop\CatalogBundle\Category\CategoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class EstimatorController
 * @package Shop\CatalogBundle\Controller
 */
class EstimatorController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function estimatorAction(Request $request)
    {

        $categoryId = $request->get('categoryId');
        $category = $categoryId ? $this->get('shop_catalog.category.repository')->findOneBy(array(
            'id' => $categoryId,
        )) : null;

        if(!$category instanceof CategoryInterface){
            throw $this->createNotFoundException('Category not found');
        }

        $estimator = $this->buildEstimator($category, $request);
        $estimatorCategories = $this->buildEstimatorCategories($request);

        return $this->render('ShopCatalogBundle:Estimator:estimator.html.twig', [
            'category' => $category,
            'estimator' => $estimator,
            'estimatorCategories' => $estimatorCategories,
        ]);


    }

    public function popupAction(Request $request){

        $categoryId = $request->get('categoryId');
        $category = $categoryId ? $this->get('shop_catalog.category.repository')->findOneBy(array(
            'id' => $categoryId,
        )) : null;

        $estimatorCategories = $this->buildEstimatorCategories($request);

        return $this->render('ShopCatalogBundle:Estimator:popup.html.twig', [
            'category' => $category,
            'estimatorCategories' => $estimatorCategories,
        ]);

    }

    /**
     * @param CategoryInterface $category
     * @param Request $request
     * @return \Shop\CatalogBundle\Estimator\Estimator|null
     */
    protected function buildEstimator(CategoryInterface $category, Request $request){

        $storageData = json_decode($request->cookies->get('proposalEstimator'), true);
        return $this->getEstimatorBuilder()->build($category, $storageData);

    }

    /**
     * @param Request $request
     * @return \Shop\CatalogBundle\Estimator\EstimatorCategory[]
     */
    protected function buildEstimatorCategories(Request $request){

        $storageData = json_decode($request->cookies->get('proposalEstimator'), true);
        return $this->getEstimatorBuilder()->buildEstimatorCategories($storageData);

    }

    /**
     * @return \Shop\CatalogBundle\Estimator\EstimatorBuilder
     */
    protected function getEstimatorBuilder(){
        return $this->get('shop_catalog.proposal.estimator.builder');
    }

}
