<?php
namespace Shop\ShippingBundle\Calculator;
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
class ShippingCalculator {

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

    public function calculate($orderCategories, $orderSummaryPrice, $city){

        $orderCategoryIds = array_keys($orderCategories);
        //$categoryShippingPrices = array();

        //var_dump($categoryIds);
        //var_dump($city);

        if($orderCategoryIds && is_array($orderCategoryIds) && $orderSummaryPrice && $city instanceof City){

            $shippingMethod = $this->getShippingMethodRepository()->getCityShippingMethods($city);

            if($shippingMethod instanceof ShippingMethod){

                foreach($orderCategoryIds as $orderCategoryId){

                    $currentShippingPrice = null;
                    $currentShippingLiftingPrice = null;
                    $currentShippingAssemblyPrice = null;

                    $orderCategory = $orderCategories[$orderCategoryId];

                    if(!isset($orderCategory['category']))
                        continue;

                    $category = $orderCategory['category'];
                    if(!$category instanceof CategoryInterface)
                        continue;

                    var_dump($category->getName());

                    $shippingCategory = $this->getShippingMethodCategoryRepository()->getShippingMethodCategory($shippingMethod->getId(), $orderCategoryId);

                    if($shippingCategory instanceof ShippingMethodCategory){

                        foreach($shippingCategory->getPrices() as $shippingCategoryPrice){

                            $this->processShippingPrice($shippingCategoryPrice, $orderSummaryPrice, $currentShippingPrice);

                        }

                    }

                    if(!$currentShippingPrice instanceof ShippingPrice){

                        foreach($shippingMethod->getPrices() as $shippingMethodPrice){

                            $this->processShippingPrice($shippingMethodPrice, $orderSummaryPrice, $currentShippingPrice);

                        }

                    }

                    if($currentShippingPrice instanceof ShippingPrice){
                        var_dump($this->getCurrencyConverter()->convert($currentShippingPrice));
                    }

                    switch($currentShippingPrice ? $currentShippingPrice->getLiftingType() : null){
                        case ShippingPrice::LIFTING_TYPE_INCLUDED:
                            //@TODO create logic
                            break;
                        case ShippingPrice::LIFTING_TYPE_IGNORE:
                            //@TODO create logic
                            break;
                        case ShippingPrice::LIFTING_TYPE_BASIC:
                        default:

                            if($shippingCategory instanceof ShippingMethodCategory){

                                foreach($shippingCategory->getLiftingPrices() as $shippingCategoryAssemblyPrice){

                                    $this->processShippingLiftingPrice($shippingCategoryAssemblyPrice, $currentShippingLiftingPrice);

                                }

                            }

                            if(!$currentShippingLiftingPrice instanceof ShippingLiftingPrice){

                                foreach($shippingMethod->getLiftingPrices() as $shippingMethodAssemblyPrice){

                                    $this->processShippingLiftingPrice($shippingMethodAssemblyPrice, $currentShippingLiftingPrice);

                                }

                            }

                            if($currentShippingLiftingPrice instanceof ShippingLiftingPrice){
                                var_dump($this->getCurrencyConverter()->convert($currentShippingLiftingPrice->getNoLiftPriceValue(), $currentShippingLiftingPrice->getNoLiftPriceCurrencyNumericCode()));
                                var_dump($this->getCurrencyConverter()->convert($currentShippingLiftingPrice->getLiftPriceValue(), $currentShippingLiftingPrice->getLiftPriceCurrencyNumericCode()));
                                var_dump($this->getCurrencyConverter()->convert($currentShippingLiftingPrice->getServiceLiftPriceValue(), $currentShippingLiftingPrice->getServiceLiftPriceCurrencyNumericCode()));
                            }

                    }

                    switch($currentShippingPrice ? $currentShippingPrice->getAssemblyType() : null){
                        case ShippingPrice::ASSEMBLY_TYPE_INCLUDED:
                            //@TODO create logic
                            break;
                        case ShippingPrice::ASSEMBLY_TYPE_IGNORE:
                            //@TODO create logic
                            break;
                        case ShippingPrice::ASSEMBLY_TYPE_BASIC:
                        default:

                            if($shippingCategory instanceof ShippingMethodCategory){

                                foreach($shippingCategory->getAssemblyPrices() as $shippingCategoryAssemblyPrice){

                                    $this->processShippingAssemblyPrice($shippingCategoryAssemblyPrice, $currentShippingAssemblyPrice);

                                }

                            }

                            if(!$currentShippingAssemblyPrice instanceof ShippingAssemblyPrice){

                                foreach($shippingMethod->getAssemblyPrices() as $shippingMethodAssemblyPrice){

                                    $this->processShippingAssemblyPrice($shippingMethodAssemblyPrice, $currentShippingAssemblyPrice);

                                }

                            }

                            if($currentShippingAssemblyPrice instanceof ShippingAssemblyPrice){
                                var_dump($this->getCurrencyConverter()->convert($currentShippingAssemblyPrice));
                            }

                    }

                }

            }

        }

        die;

    }

    /**
     * @param $shippingPrice
     * @param $orderSummaryPrice
     * @param $currentShippingPrice
     */
    protected function processShippingPrice($shippingPrice, $orderSummaryPrice, &$currentShippingPrice){

        if(!$shippingPrice instanceof ShippingPrice)
            return;

        switch($shippingPrice->getOrderPriceType()){
            case $shippingPrice::ORDER_PRICE_TYPE_RANGE:

                $minOrderPriceValue = $this->getCurrencyConverter()->convert($shippingPrice->getMinOrderPriceValue(), $shippingPrice->getMinOrderPriceCurrencyNumericCode());
                $maxOrderPriceValue = $this->getCurrencyConverter()->convert($shippingPrice->getMaxOrderPriceValue(), $shippingPrice->getMaxOrderPriceCurrencyNumericCode());

                if((!$minOrderPriceValue || $orderSummaryPrice >= $minOrderPriceValue) && (!$maxOrderPriceValue || $orderSummaryPrice <= $maxOrderPriceValue)){

                    $currentShippingPrice = $shippingPrice;

                }

                break;

            case $shippingPrice::ORDER_PRICE_TYPE_ANY:
            default:

                if(!$currentShippingPrice instanceof ShippingPrice){

                    $currentShippingPrice = $shippingPrice;

                }

        }

    }

    /**
     * @param $shippingLiftingPrice
     * @param $currentShippingLiftingPrice
     */
    protected function processShippingLiftingPrice($shippingLiftingPrice, &$currentShippingLiftingPrice){

        if(!$shippingLiftingPrice instanceof ShippingLiftingPrice)
            return;

        switch($shippingLiftingPrice->getPriceType()){
            case $shippingLiftingPrice::PRICE_TYPE_PER_FLOOR:

                $currentShippingLiftingPrice = $shippingLiftingPrice;
                break;

            case $shippingLiftingPrice::PRICE_TYPE_ANY_FLOOR:
            default:

                if(!$currentShippingLiftingPrice instanceof ShippingLiftingPrice){
                    $currentShippingLiftingPrice = $shippingLiftingPrice;
                }

        }

    }

    /**
     * @param $shippingAssemblyPrice
     * @param $currentShippingAssemblyPrice
     */
    protected function processShippingAssemblyPrice($shippingAssemblyPrice, &$currentShippingAssemblyPrice){

        if(!$shippingAssemblyPrice instanceof ShippingAssemblyPrice)
            return;

        if(!$currentShippingAssemblyPrice instanceof ShippingAssemblyPrice){
            $currentShippingAssemblyPrice = $shippingAssemblyPrice;
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