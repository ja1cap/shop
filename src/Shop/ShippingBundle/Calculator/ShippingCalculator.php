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
use Weasty\CatalogBundle\Data\CategoryInterface;
use Weasty\GeonamesBundle\Entity\City;
use Weasty\MoneyBundle\Converter\CurrencyConverterInterface;
use Weasty\MoneyBundle\Data\PriceInterface;

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
     * @return ShippingCalculatorResultInterface
     */
    public function calculate($options = null){

        $optionsCollection = new ArrayCollection($options ?: array());

        $shopCartSummaryCategories = $optionsCollection->get('shopCartSummaryCategories');
        $shopCartSummaryPrice = $optionsCollection->get('shopCartSummaryPrice');
        $city = $optionsCollection->get('city');

        $shippingCalculatorResult = new ShippingCalculatorResult();

        if($shopCartSummaryCategories instanceof Collection){
            $shopCartSummaryCategories = $shopCartSummaryCategories->toArray();
        }

        if($shopCartSummaryPrice instanceof PriceInterface){
            $shopCartSummaryPrice = $shopCartSummaryPrice->getValue();
        }

        if ($shopCartSummaryCategories && is_array($shopCartSummaryCategories) && $city instanceof City) {

            $shippingCalculatorResult
                ->setCity($city)
                ->setLiftType($optionsCollection->get('liftType'))
                ->setFloor($optionsCollection->get('floor'))
            ;

            $this
                ->buildCategoryShippingCalculators($shopCartSummaryCategories, $shopCartSummaryPrice, $city)
                ->map(function($shippingCalculator) use ($options, $shippingCalculatorResult) {
                    if($shippingCalculator instanceof ShippingCalculatorInterface){
                        $shippingCalculatorResult->addShippingSummary($shippingCalculator->calculate($options));
                    }
                })
            ;

        }

        return $shippingCalculatorResult;

    }

    /**
     * @param $shopCartSummaryCategories
     * @param $shopCartSummaryPrice
     * @param $city
     * @return ArrayCollection
     */
    protected function buildCategoryShippingCalculators($shopCartSummaryCategories, $shopCartSummaryPrice, City $city)
    {

        $categoryShippingCalculators = new ArrayCollection();

        $categoryIds = array_keys($shopCartSummaryCategories);

        $shippingMethod = $this->getShippingMethodRepository()->getCityShippingMethods($city);

        if ($shippingMethod instanceof ShippingMethod) {

            foreach ($categoryIds as $categoryId) {

                $currentShippingAssemblyPrice = null;

                $shopCartSummaryCategory = $shopCartSummaryCategories[$categoryId];

                if (!isset($shopCartSummaryCategory['category']))
                    continue;

                $category = $shopCartSummaryCategory['category'];
                if (!$category instanceof CategoryInterface)
                    continue;

                $categoryShippingCalculator = new CategoryShippingCalculator($category, $this->getCurrencyConverter());
                $categoryShippingCalculators->set($category->getId(), $categoryShippingCalculator);

                $shippingCategory = $this->getShippingMethodCategoryRepository()->getShippingMethodCategory($shippingMethod->getId(), $categoryId);

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

        if(!$shippingPrice instanceof ShippingPrice || !$categoryShippingCalculator instanceof CategoryShippingCalculator)
            return;

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