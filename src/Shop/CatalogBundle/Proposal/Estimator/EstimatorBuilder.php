<?php
namespace Shop\CatalogBundle\Proposal\Estimator;

use Shop\CatalogBundle\Category\CategoryInterface;
use Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeaturesBuilder;

/**
 * Class EstimatorBuilder
 * @package Shop\CatalogBundle\Proposal\Estimator
 */
class EstimatorBuilder {

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $categoryRepository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $proposalRepository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $priceRepository;

    function __construct($categoryRepository, $proposalRepository, $priceRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->proposalRepository = $proposalRepository;
        $this->priceRepository = $priceRepository;
    }

    /**
     * @param CategoryInterface $category
     * @param $storageData
     * @return null|Estimator
     */
    public function build(CategoryInterface $category, $storageData){

        $estimator = null;

        if(isset($storageData['categories']) && is_array($storageData['categories']) && isset($storageData['categories'][$category->getId()])){

            $estimatedFeaturesBuilder = new EstimatedFeaturesBuilder();

            $categoryData = $storageData['categories'][$category->getId()];

            $estimator = new Estimator();
            $estimator->setCategory($category);

            $prices = [];

            $proposalPricesData = $categoryData['proposalPrices'];
            foreach($proposalPricesData as $proposalPriceData){

                $priceId = $proposalPriceData['priceId'];
                $price = $this->findPrice($priceId);
                $prices[$priceId] = $price;

                $proposalId = $proposalPriceData['proposalId'];
                $proposal = $this->findProposal($proposalId);

                $estimatedProposal = new EstimatedProposal($proposal, $price);
                $estimator->addEstimatedProposal($priceId, $estimatedProposal);

            }

            $estimatedFeaturesBuilder->setEstimator($estimator);

            $estimatedFeatures = $estimatedFeaturesBuilder->build($category, $prices);
            $estimator->setEstimatedFeatures($estimatedFeatures);

        }

        return $estimator;

    }

    /**
     * @param $categoryId
     * @return \Shop\CatalogBundle\Category\CategoryInterface
     */
    protected function findCategory($categoryId){
        return $this->categoryRepository->findOneBy(array(
            'id' => (int)$categoryId,
        ));
    }

    /**
     * @param $proposalId
     * @return \Shop\CatalogBundle\Proposal\ProposalInterface
     */
    protected function findProposal($proposalId){
        return $this->proposalRepository->findOneBy(array(
            'id' => (int)$proposalId,
        ));
    }

    /**
     * @param $priceId
     * @return \Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface
     */
    protected function findPrice($priceId){
        return $this->priceRepository->findOneBy(array(
            'id' => (int)$priceId,
        ));
    }

} 