<?php
namespace Shop\ShippingBundle\Calculator;
use Doctrine\Common\Collections\ArrayCollection;
use Shop\ShippingBundle\Entity\ShippingAssemblyPrice;
use Shop\ShippingBundle\Entity\ShippingLiftingPrice;
use Shop\ShippingBundle\Entity\ShippingMethod;
use Shop\ShippingBundle\Entity\ShippingMethodCategory;
use Shop\ShippingBundle\Entity\ShippingMethodCategoryRepository;
use Shop\ShippingBundle\Entity\ShippingMethodRepository;
use Shop\ShippingBundle\Entity\ShippingPrice;
use Weasty\CatalogBundle\Data\CategoryInterface;
use Weasty\GeonamesBundle\Entity\City;
use Weasty\MoneyBundle\Converter\CurrencyConverterInterface;

/**
 * Class ShippingCalculator
 * @package Shop\ShippingBundle\Calculator
 */
class ShippingCalculator implements ShippingCalculatorInterface {

    /**
     * @var \Shop\ShippingBundle\Entity\ShippingMethodRepository
     */
    protected $shippingMethodRepository;

    /**
     * @var \Shop\ShippingBundle\Entity\ShippingMethodCategoryRepository
     */
    protected $shippingMethodCategoryRepository;

    /**
     * @var \Weasty\MoneyBundle\Converter\CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @var \Weasty\MoneyBundle\Converter\CurrencyConverterInterface
     */
    protected $proposalPriceCurrencyConverter;

    function __construct(ShippingMethodRepository $shippingMethodRepository, ShippingMethodCategoryRepository $shippingMethodCategoryRepository, CurrencyConverterInterface $currencyConverter, CurrencyConverterInterface $proposalPriceCurrencyConverter)
    {
        $this->shippingMethodRepository = $shippingMethodRepository;
        $this->shippingMethodCategoryRepository = $shippingMethodCategoryRepository;
        $this->currencyConverter = $currencyConverter;
        $this->proposalPriceCurrencyConverter = $proposalPriceCurrencyConverter;
    }

    /**
     * @param null $options
     * @return mixed|ShippingSummariesCollection
     */
    public function calculate($options = null){

        $optionsCollection = new ArrayCollection($options ?: array());

        $orderCategories = $optionsCollection->get('orderCategories');
        $orderSummaryPrice = $optionsCollection->get('orderSummaryPrice');
        $city = $optionsCollection->get('city');

        $summariesCollection = new ShippingSummariesCollection();

        if ($orderCategories && is_array($orderCategories) && $city instanceof City) {

            $this
                ->buildCategoryShippingCalculators($orderCategories, $orderSummaryPrice, $city)
                ->map(function($shippingCalculator) use ($options, $summariesCollection) {
                    if($shippingCalculator instanceof ShippingCalculatorInterface){
                        $summariesCollection->add($shippingCalculator->calculate($options));
                    }
                })
            ;

        }

        return $summariesCollection;

    }

    /**
     * @param $orderCategories
     * @param $orderSummaryPrice
     * @param $city
     * @return ArrayCollection
     */
    protected function buildCategoryShippingCalculators(array $orderCategories, $orderSummaryPrice, City $city)
    {

        $categoryShippingCalculators = new ArrayCollection();

        $orderCategoryIds = array_keys($orderCategories);

        $shippingMethod = $this->getShippingMethodRepository()->getCityShippingMethods($city);

        if ($shippingMethod instanceof ShippingMethod) {

            foreach ($orderCategoryIds as $orderCategoryId) {

                $currentShippingAssemblyPrice = null;

                $orderCategory = $orderCategories[$orderCategoryId];

                if (!isset($orderCategory['category']))
                    continue;

                $category = $orderCategory['category'];
                if (!$category instanceof CategoryInterface)
                    continue;

                $categoryShippingCalculator = new CategoryShippingCalculator($category, $this->getCurrencyConverter());
                $categoryShippingCalculators->set($category->getId(), $categoryShippingCalculator);

                $shippingCategory = $this->getShippingMethodCategoryRepository()->getShippingMethodCategory($shippingMethod->getId(), $orderCategoryId);

                if ($shippingCategory instanceof ShippingMethodCategory) {
                    foreach ($shippingCategory->getPrices() as $shippingCategoryPrice) {
                        $this->processShippingPrice($shippingCategoryPrice, $orderSummaryPrice, $categoryShippingCalculator);
                    }
                }

                if (!$categoryShippingCalculator->getShippingPrice() instanceof ShippingPrice) {
                    foreach ($shippingMethod->getPrices() as $shippingMethodPrice) {
                        $this->processShippingPrice($shippingMethodPrice, $orderSummaryPrice, $categoryShippingCalculator);
                    }
                }

                $shippingPrice = $categoryShippingCalculator->getShippingPrice();

                switch ($shippingPrice ? $shippingPrice->getLiftingType() : null) {
                    case ShippingPrice::LIFTING_TYPE_INCLUDED:
                        //@TODO create logic
                        break;
                    case ShippingPrice::LIFTING_TYPE_IGNORE:
                        //@TODO create logic
                        break;
                    case ShippingPrice::LIFTING_TYPE_BASIC:
                    default:

                        if ($shippingCategory instanceof ShippingMethodCategory) {
                            foreach ($shippingCategory->getLiftingPrices() as $shippingCategoryAssemblyPrice) {
                                $this->processShippingLiftingPrice($shippingCategoryAssemblyPrice, $categoryShippingCalculator);
                            }
                        }

                        if (!$categoryShippingCalculator->getShippingLiftingPrice() instanceof ShippingLiftingPrice) {
                            foreach ($shippingMethod->getLiftingPrices() as $shippingMethodAssemblyPrice) {
                                $this->processShippingLiftingPrice($shippingMethodAssemblyPrice, $categoryShippingCalculator);
                            }
                        }

                }

                switch ($shippingPrice ? $shippingPrice->getAssemblyType() : null) {
                    case ShippingPrice::ASSEMBLY_TYPE_INCLUDED:
                        //@TODO create logic
                        break;
                    case ShippingPrice::ASSEMBLY_TYPE_IGNORE:
                        //@TODO create logic
                        break;
                    case ShippingPrice::ASSEMBLY_TYPE_BASIC:
                    default:

                        if ($shippingCategory instanceof ShippingMethodCategory) {
                            foreach ($shippingCategory->getAssemblyPrices() as $shippingCategoryAssemblyPrice) {
                                $this->processShippingAssemblyPrice($shippingCategoryAssemblyPrice, $categoryShippingCalculator);
                            }
                        }

                        if (!$currentShippingAssemblyPrice instanceof ShippingAssemblyPrice) {
                            foreach ($shippingMethod->getAssemblyPrices() as $shippingMethodAssemblyPrice) {
                                $this->processShippingAssemblyPrice($shippingMethodAssemblyPrice, $categoryShippingCalculator);
                            }
                        }

                }

            }

        }

        return $categoryShippingCalculators;
    }

    /**
     * @param $shippingPrice
     * @param $orderSummaryPrice
     * @param $categoryShippingCalculator
     */
    protected function processShippingPrice($shippingPrice, $orderSummaryPrice, $categoryShippingCalculator){

        if(!$shippingPrice instanceof ShippingPrice || !$categoryShippingCalculator instanceof CategoryShippingCalculator)
            return;

        switch($shippingPrice->getOrderPriceType()){
            case $shippingPrice::ORDER_PRICE_TYPE_RANGE:

                $minOrderPriceValue = $this->getCurrencyConverter()->convert($shippingPrice->getMinOrderPriceValue(), $shippingPrice->getMinOrderPriceCurrencyNumericCode());
                $maxOrderPriceValue = $this->getCurrencyConverter()->convert($shippingPrice->getMaxOrderPriceValue(), $shippingPrice->getMaxOrderPriceCurrencyNumericCode());

                if((!$minOrderPriceValue || $orderSummaryPrice >= $minOrderPriceValue) && (!$maxOrderPriceValue || $orderSummaryPrice <= $maxOrderPriceValue)){

                    $categoryShippingCalculator->setShippingPrice($shippingPrice);

                }

                break;

            case $shippingPrice::ORDER_PRICE_TYPE_ANY:
            default:

                if(!$categoryShippingCalculator->getShippingPrice() instanceof ShippingPrice){

                    $categoryShippingCalculator->setShippingPrice($shippingPrice);

                }

        }

    }

    /**
     * @param $shippingLiftingPrice
     * @param $categoryShippingCalculator
     */
    protected function processShippingLiftingPrice($shippingLiftingPrice, $categoryShippingCalculator){

        if(!$shippingLiftingPrice instanceof ShippingLiftingPrice || !$categoryShippingCalculator instanceof CategoryShippingCalculator)
            return;

        switch($shippingLiftingPrice->getPriceType()){
            case $shippingLiftingPrice::PRICE_TYPE_PER_FLOOR:

                $categoryShippingCalculator->setShippingLiftingPrice($shippingLiftingPrice);
                break;

            case $shippingLiftingPrice::PRICE_TYPE_ANY_FLOOR:
            default:

                if(!$categoryShippingCalculator->getShippingLiftingPrice() instanceof ShippingLiftingPrice){
                    $categoryShippingCalculator->setShippingLiftingPrice($shippingLiftingPrice);
                }

        }

    }

    /**
     * @param $shippingAssemblyPrice
     * @param $categoryShippingCalculator
     */
    protected function processShippingAssemblyPrice($shippingAssemblyPrice, $categoryShippingCalculator){

        if(!$shippingAssemblyPrice instanceof ShippingAssemblyPrice || !$categoryShippingCalculator instanceof CategoryShippingCalculator)
            return;

        if(!$categoryShippingCalculator->getShippingAssemblyPrice() instanceof ShippingAssemblyPrice){
            $categoryShippingCalculator->setShippingAssemblyPrice($shippingAssemblyPrice);
        }

    }

    /**
     * @return CurrencyConverterInterface
     */
    public function getCurrencyConverter()
    {
        return $this->currencyConverter;
    }

    /**
     * @return CurrencyConverterInterface
     */
    public function getProposalPriceCurrencyConverter()
    {
        return $this->proposalPriceCurrencyConverter;
    }

    /**
     * @return \Shop\ShippingBundle\Entity\ShippingMethodRepository
     */
    public function getShippingMethodRepository()
    {
        return $this->shippingMethodRepository;
    }

    /**
     * @return \Shop\ShippingBundle\Entity\ShippingMethodCategoryRepository
     */
    public function getShippingMethodCategoryRepository()
    {
        return $this->shippingMethodCategoryRepository;
    }

}