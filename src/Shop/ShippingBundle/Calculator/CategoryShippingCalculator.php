<?php
namespace Shop\ShippingBundle\Calculator;

use Doctrine\Common\Collections\ArrayCollection;
use Shop\ShippingBundle\Entity\ShippingLiftingPrice;
use Weasty\Bundle\CatalogBundle\Category\CategoryInterface;
use Weasty\Money\Currency\Converter\CurrencyConverterInterface;
use Weasty\Money\Price\Price;
use Weasty\Money\Price\PriceInterface;

/**
 * Class CategoryShippingCalculator
 * @package Shop\ShippingBundle\Calculator
 */
class CategoryShippingCalculator implements ShippingCalculatorInterface {

    /**
     * @var \Weasty\Money\Currency\Converter\CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @var CategoryInterface
     */
    protected $category;

    /**
     * @var \Shop\ShippingBundle\Entity\ShippingPrice
     */
    protected $shippingPrice;

    /**
     * @var \Shop\ShippingBundle\Entity\ShippingLiftingPrice
     */
    protected $shippingLiftingPrice;

    /**
     * @var \Shop\ShippingBundle\Entity\ShippingAssemblyPrice
     */
    protected $shippingAssemblyPrice;

    function __construct(CategoryInterface $category, CurrencyConverterInterface $currencyConverter)
    {
        $this->category = $category;
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * @param $options
     * @return ShippingSummaryInterface
     */
    public function calculate($options = null)
    {

        $optionsCollection = new ArrayCollection($options ?: array());

        $summary = new CategoryShippingSummary($this->getCategory());

        $summaryPriceValue = 0;
        $summaryPriceCurrency = $this->getCurrencyConverter()->getCurrencyResource()->getDefaultCurrency();

        if($this->getShippingPrice() instanceof PriceInterface){

            $summary->setShippingPrice($this->getShippingPrice());
            $summaryPriceValue += $this->getCurrencyConverter()->convert($this->getShippingPrice());

        }

        $shippingLiftingPrice = $this->getShippingLiftingPrice();
        if($shippingLiftingPrice instanceof ShippingLiftingPrice){

            switch($optionsCollection->get('liftType')){
                case ShippingLiftingPrice::LIFT_TYPE_NO_LIFT:

                    $liftingPriceValue = $shippingLiftingPrice->getNoLiftPriceValue();
                    $liftingPriceCurrency = $shippingLiftingPrice->getNoLiftPriceCurrencyNumericCode();
                    break;

                case ShippingLiftingPrice::LIFT_TYPE_SERVICE_LIFT:

                    $liftingPriceValue = $shippingLiftingPrice->getServiceLiftPriceValue();
                    $liftingPriceCurrency = $shippingLiftingPrice->getServiceLiftPriceCurrencyNumericCode();
                    break;

                case ShippingLiftingPrice::LIFT_TYPE_LIFT:
                default:

                    $liftingPriceValue = $shippingLiftingPrice->getLiftPriceValue();
                    $liftingPriceCurrency = $shippingLiftingPrice->getLiftPriceCurrencyNumericCode();
                    break;


            }

            switch($shippingLiftingPrice->getPriceType()){
                case ShippingLiftingPrice::PRICE_TYPE_PER_FLOOR:

                    $floor = $optionsCollection->get('floor') ?: 9;
                    $liftingPriceValue *= $floor;

                    break;

            }

            $liftingPrice = new Price($liftingPriceValue, $liftingPriceCurrency);
            $summaryPriceValue += $this->getCurrencyConverter()->convert($liftingPrice);

            $summary->setLiftingPrice($liftingPrice);

        }

        if($this->getShippingAssemblyPrice() instanceof PriceInterface){

            $summaryPriceValue += $this->getCurrencyConverter()->convert($this->getShippingAssemblyPrice());
            $summary->setAssemblyPrice($this->getShippingAssemblyPrice());

        }

        $summaryPrice = new Price($summaryPriceValue, $summaryPriceCurrency);
        $summary->setSummaryPrice($summaryPrice);

        return $summary;

    }

    /**
     * @return \Weasty\Bundle\CatalogBundle\Category\CategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return \Shop\ShippingBundle\Entity\ShippingAssemblyPrice
     */
    public function getShippingAssemblyPrice()
    {
        return $this->shippingAssemblyPrice;
    }

    /**
     * @param \Shop\ShippingBundle\Entity\ShippingAssemblyPrice $shippingAssemblyPrice
     */
    public function setShippingAssemblyPrice($shippingAssemblyPrice)
    {
        $this->shippingAssemblyPrice = $shippingAssemblyPrice;
    }

    /**
     * @return \Shop\ShippingBundle\Entity\ShippingLiftingPrice
     */
    public function getShippingLiftingPrice()
    {
        return $this->shippingLiftingPrice;
    }

    /**
     * @param \Shop\ShippingBundle\Entity\ShippingLiftingPrice $shippingLiftingPrice
     */
    public function setShippingLiftingPrice($shippingLiftingPrice)
    {
        $this->shippingLiftingPrice = $shippingLiftingPrice;
    }

    /**
     * @return \Shop\ShippingBundle\Entity\ShippingPrice
     */
    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }

    /**
     * @param \Shop\ShippingBundle\Entity\ShippingPrice $shippingPrice
     */
    public function setShippingPrice($shippingPrice)
    {
        $this->shippingPrice = $shippingPrice;
    }

    /**
     * @return \Weasty\Money\Currency\Converter\CurrencyConverterInterface
     */
    public function getCurrencyConverter()
    {
        return $this->currencyConverter;
    }

} 