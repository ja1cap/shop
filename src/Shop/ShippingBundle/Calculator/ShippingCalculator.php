<?php
namespace Shop\ShippingBundle\Calculator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Shop\ShippingBundle\Entity\ShippingAssemblyPrice;
use Shop\ShippingBundle\Entity\ShippingLiftingPrice;
use Shop\ShippingBundle\Entity\ShippingMethod;
use Shop\ShippingBundle\Entity\ShippingMethodCategory;
use Shop\ShippingBundle\Entity\ShippingMethodCategoryRepository;
use Shop\ShippingBundle\Entity\ShippingMethodRepository;
use Shop\ShippingBundle\Entity\ShippingPrice;
use Weasty\Bundle\CatalogBundle\Data\CategoryInterface;
use Weasty\Bundle\GeonamesBundle\Entity\City;
use Weasty\Money\Currency\Converter\CurrencyConverterInterface;
use Weasty\Money\Price\PriceInterface;

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
     * @var \Weasty\Money\Currency\Converter\CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @var \Weasty\Money\Currency\Converter\CurrencyConverterInterface
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
     * @return ShippingCalculatorResultInterface
     */
    public function calculate($options = null){

        $optionsCollection = new ArrayCollection($options ?: array());

        $shopCartCategories = $optionsCollection->get('shopCartCategories');
        $shopCartSummaryPrice = $optionsCollection->get('shopCartSummaryPrice');
        $city = $optionsCollection->get('city');

        $shippingCalculatorResult = new ShippingCalculatorResult();

        if($shopCartCategories instanceof Collection){
            $shopCartCategories = $shopCartCategories->toArray();
        }

        if($shopCartSummaryPrice instanceof PriceInterface){
            $shopCartSummaryPrice = $shopCartSummaryPrice->getValue();
        }

        if ($shopCartCategories && is_array($shopCartCategories) && $city instanceof City) {

            $shippingCalculatorResult
                ->setCity($city)
                ->setLiftType($optionsCollection->get('liftType') ?: ShippingLiftingPrice::LIFT_TYPE_LIFT)
                ->setFloor($optionsCollection->get('floor') ?: 10)
            ;

            $categoryShippingCalculators = $this->buildCategoryShippingCalculators($shopCartCategories, $shopCartSummaryPrice, $city);

            /**
             * @var $categoryShippingCalculator ShippingCalculatorInterface
             */
            foreach($categoryShippingCalculators as $categoryShippingCalculator){
                $shippingCalculatorResult->addShippingSummary($categoryShippingCalculator->calculate($options));
            }

        }

        return $shippingCalculatorResult;

    }

    /**
     * @param $shopCartCategories
     * @param $shopCartSummaryPrice
     * @param $city
     * @return ArrayCollection
     */
    protected function buildCategoryShippingCalculators($shopCartCategories, $shopCartSummaryPrice, City $city)
    {

        $categoryShippingCalculators = new ArrayCollection();

        $shippingMethod = $this->getShippingMethodRepository()->getCityShippingMethods($city);

        if ($shippingMethod instanceof ShippingMethod) {

            foreach ($shopCartCategories as $shopCartCategory) {

                $currentShippingAssemblyPrice = null;

                if($shopCartCategory instanceof CategoryInterface){

                    $category = $shopCartCategory;

                } else {

                    if (!isset($shopCartCategory['category']))
                        continue;

                    $category = $shopCartCategory['category'];
                    if (!$category instanceof CategoryInterface)
                        continue;

                }

                $categoryShippingCalculator = new CategoryShippingCalculator($category, $this->getCurrencyConverter());
                $categoryShippingCalculators->set($category->getId(), $categoryShippingCalculator);

                $shippingCategory = $this->getShippingMethodCategoryRepository()->getShippingMethodCategory($shippingMethod->getId(), $category->getId());

                if ($shippingCategory instanceof ShippingMethodCategory) {
                    foreach ($shippingCategory->getPrices() as $shippingCategoryPrice) {
                        $this->processShippingPrice($shippingCategoryPrice, $shopCartSummaryPrice, $categoryShippingCalculator);
                    }
                }

                if (!$categoryShippingCalculator->getShippingPrice() instanceof ShippingPrice) {
                    foreach ($shippingMethod->getPrices() as $shippingMethodPrice) {
                        $this->processShippingPrice($shippingMethodPrice, $shopCartSummaryPrice, $categoryShippingCalculator);
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
     * @param $shopCartSummaryPrice
     * @param $categoryShippingCalculator
     */
    protected function processShippingPrice($shippingPrice, $shopCartSummaryPrice, $categoryShippingCalculator){

        if($shippingPrice instanceof ShippingPrice && $categoryShippingCalculator instanceof CategoryShippingCalculator){

            switch($shippingPrice->getOrderPriceType()){
                case $shippingPrice::ORDER_PRICE_TYPE_RANGE:

                    $minOrderPriceValue = $this->getCurrencyConverter()->convert($shippingPrice->getMinOrderPriceValue(), $shippingPrice->getMinOrderPriceCurrencyNumericCode());
                    $maxOrderPriceValue = $this->getCurrencyConverter()->convert($shippingPrice->getMaxOrderPriceValue(), $shippingPrice->getMaxOrderPriceCurrencyNumericCode());

                    if((!$minOrderPriceValue || $shopCartSummaryPrice >= $minOrderPriceValue) && (!$maxOrderPriceValue || $shopCartSummaryPrice <= $maxOrderPriceValue)){

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

    }

    /**
     * @param $shippingLiftingPrice
     * @param $categoryShippingCalculator
     */
    protected function processShippingLiftingPrice($shippingLiftingPrice, $categoryShippingCalculator){

        if($shippingLiftingPrice instanceof ShippingLiftingPrice && $categoryShippingCalculator instanceof CategoryShippingCalculator){

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

    }

    /**
     * @param $shippingAssemblyPrice
     * @param $categoryShippingCalculator
     */
    protected function processShippingAssemblyPrice($shippingAssemblyPrice, $categoryShippingCalculator){

        if($shippingAssemblyPrice instanceof ShippingAssemblyPrice && $categoryShippingCalculator instanceof CategoryShippingCalculator){

            if(!$categoryShippingCalculator->getShippingAssemblyPrice() instanceof ShippingAssemblyPrice){
                $categoryShippingCalculator->setShippingAssemblyPrice($shippingAssemblyPrice);
            }

        }

    }

    /**
     * @return \Weasty\Money\Currency\Converter\CurrencyConverterInterface
     */
    public function getCurrencyConverter()
    {
        return $this->currencyConverter;
    }

    /**
     * @return \Weasty\Money\Currency\Converter\CurrencyConverterInterface
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