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
     * @param $categorySlug
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function estimatorAction($categorySlug, Request $request)
    {

        $category = $this->get('shop_catalog.category.repository')->findOneBy(array(
            'slug' => $categorySlug,
        ));

        if(!$category instanceof CategoryInterface){
            throw $this->createNotFoundException('Category not found');
        }

        $estimator = $this->buildEstimator($category, $request);

        return $this->render('ShopCatalogBundle:Estimator:estimator.html.twig', array(
            'category' => $category,
            'estimator' => $estimator,
        ));


    }

    /**
     * @param CategoryInterface $category
     * @param Request $request
     * @return \Shop\CatalogBundle\Proposal\Estimator\Estimator|null
     */
    protected function buildEstimator(CategoryInterface $category, Request $request){

        $storageData = json_decode($request->cookies->get('proposalEstimator'), true);
        $builder = $this->get('shop_catalog.proposal.estimator.builder');
        return $builder->build($category, $storageData);

    }

}
