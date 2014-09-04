<?php
namespace Shop\CatalogBundle\Proposal\Estimator;

use Shop\CatalogBundle\Category\CategoryInterface;
use Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeature;
use Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeatureInterface;
use Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeaturesBuilder;
use Shop\CatalogBundle\Proposal\Estimator\Feature\EstimatedFeatureValue;
use Shop\CatalogBundle\Proposal\Estimator\Feature\Price\PriceFeatureValue;
use Weasty\Money\Price\Price;

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

    /**
     * @var \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditionsBuilder
     */
    protected $proposalActionConditionsBuilder;

    /**
     * @var \Weasty\Money\Currency\Converter\CurrencyConverterInterface
     */
    protected $currencyConverter;

    function __construct($categoryRepository, $proposalRepository, $priceRepository, $proposalActionConditionsBuilder, $currencyConverter)
    {
        $this->categoryRepository = $categoryRepository;
        $this->proposalRepository = $proposalRepository;
        $this->priceRepository = $priceRepository;
        $this->proposalActionConditionsBuilder = $proposalActionConditionsBuilder;
        $this->currencyConverter = $currencyConverter;
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

            $priceFeature = $this->buildPriceFeature();
            $estimator->setPriceFeature($priceFeature);

            $rateFeature = $this->buildRateFeature();
            $estimator->setRateFeature($rateFeature);

            $proposalPricesData = $categoryData['proposalPrices'];
            foreach($proposalPricesData as $proposalPriceData){

                $priceId = $proposalPriceData['priceId'];
                $price = $this->findPrice($priceId);
                $prices[$priceId] = $price;

                $proposalId = $proposalPriceData['proposalId'];
                $proposal = $this->findProposal($proposalId);

                $estimatedProposal = new EstimatedProposal($proposal, $price);

                //Proposal action conditions
                $actionConditionIds = $proposalPriceData['actionConditionIds'];
                $actionConditions = $this->proposalActionConditionsBuilder->build($proposal, $actionConditionIds);
                $estimatedProposal->setActionConditions($actionConditions);

                //Proposal price feature value
                $priceFeatureValue = $this->buildPriceFeatureValue($price, $actionConditions);
                $priceFeatureValue->setFeature($priceFeature);
                $priceFeature->addFeatureValue($priceId, $priceFeatureValue);
                $estimatedProposal->setPriceFeatureValue($priceFeatureValue);

                //Proposal rate feature value
                $rateFeatureValue = $this->buildRateFeatureValue($price);
                $rateFeatureValue->setFeature($rateFeature);
                $estimatedProposal->setRateFeatureValue($rateFeatureValue);

                $estimator->addEstimatedProposal($priceId, $estimatedProposal);

            }

            $estimatedFeaturesBuilder->setEstimator($estimator);
            $estimatedFeatures = $estimatedFeaturesBuilder->build($category, $prices);
            $estimator->setEstimatedFeatures($estimatedFeatures);

        }

        return $estimator;

    }

    /**
     * @return EstimatedFeature
     */
    protected function buildRateFeature(){

        $feature = new EstimatedFeature();
        $feature
            ->setIsComparable(true)
            ->setPriorityOrder(EstimatedFeatureInterface::PRIORITY_ORDER_ASC)
        ;

        return $feature;

    }

    /**
     * @param \Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface $price
     * @return EstimatedFeatureValue
     */
    public function buildRateFeatureValue($price){

        $featureValue = new EstimatedFeatureValue();
        $featureValue
            ->setPriceId($price->getId())
            ->setProposalId($price->getProposalId())
            ->setValue(0)
            ->setPriority(0)
        ;

        return $featureValue;

    }

    /**
     * @return EstimatedFeature
     */
    protected function buildPriceFeature(){

        $feature = new EstimatedFeature();
        $feature
            ->setIsComparable(true)
            ->setPriorityOrder(EstimatedFeatureInterface::PRIORITY_ORDER_DESC)
        ;

        return $feature;
    }

    /**
     * @param \Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface $price
     * @param \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditions $actionConditions
     * @return PriceFeatureValue
     */
    protected function buildPriceFeatureValue($price, $actionConditions){

        $value = new PriceFeatureValue();
        $value
            ->setPriceId($price->getId())
            ->setProposalId($price->getProposalId())
        ;

        $itemPrice = new Price($this->currencyConverter->convert($price), $this->currencyConverter->getCurrencyResource()->getDefaultCurrency());

        if($actionConditions){

            $discountPrice = $actionConditions->getDiscountPrice($itemPrice);

            if($discountPrice){

                $value->setPrice($discountPrice);

            } else {

                $value->setPrice($itemPrice);

            }

        } else {

            $value->setPrice($itemPrice);

        }

        return $value;

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