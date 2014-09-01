<?php
namespace Shop\CatalogBundle\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Inflector;
use Shop\DiscountBundle\Price\DiscountPrice;
use Shop\DiscountBundle\Price\DiscountPriceInterface;
use Weasty\Money\Price\Price;

/**
 * Class ShopCart
 * @package Shop\CatalogBundle\Cart
 */
class ShopCart implements \ArrayAccess {

    /**
     * @var mixed
     */
    protected $customerCity;

    /**
     * @var integer|null
     */
    protected $customerFloor;

    /**
     * @var integer|null
     */
    protected $customerLiftType;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $categories;

    /**
     * @var \Shop\ShippingBundle\Calculator\ShippingCalculator
     */
    protected $shippingCalculator;

    /**
     * @var \Weasty\Money\Currency\Converter\CurrencyConverterInterface
     */
    protected $currencyConverter;

    function __construct($shippingCalculator, $currencyConverter)
    {
        $this->shippingCalculator = $shippingCalculator;
        $this->currencyConverter = $currencyConverter;
        $this->categories = new ArrayCollection();
    }

    /**
     * @return ShopCartPrice[]
     */
    protected function getShopCartPrices(){

        $shopCartPrices = array();

        /**
         * @var $shopCartCategory ShopCartCategory
         */
        foreach($this->getCategories() as $shopCartCategory){

            /**
             * @var $shopCartProposal ShopCartProposal
             */
            foreach($shopCartCategory->getProposals() as $shopCartProposal){

                /**
                 * @var $shopCartPrice ShopCartPrice
                 */
                foreach($shopCartProposal->getPrices() as $shopCartPrice){

                    $shopCartPrices[] = $shopCartPrice;

                }

            }

        }

        return $shopCartPrices;

    }

    /**
     * @return \Weasty\Money\Price\PriceInterface|\Shop\DiscountBundle\Price\DiscountPriceInterface|null
     */
    public function getSummaryPrice(){

        $shopCartPrices = $this->getShopCartPrices();

        if(count($shopCartPrices) == 1){

            $shopCartPrice = current($shopCartPrices);
            if($shopCartPrice instanceof ShopCartPrice){

                return $shopCartPrice->getSummary();

            } else {

                return null;

            }

        } else {

            $summaryPriceValue = 0;
            $summaryPriceCurrency = null;

            $hasDiscount = false;
            $discountSummaryOriginalValue = 0;

            /**
             * @var $shopCartPrice ShopCartPrice
             */
            foreach($shopCartPrices as $shopCartPrice){

                $summaryPrice = $shopCartPrice->getSummaryPrice();
                $summaryPriceValue += $summaryPrice->getValue();
                $summaryPriceCurrency = $summaryPrice->getCurrency();

                if($summaryPrice instanceof DiscountPriceInterface){

                    $hasDiscount = true;
                    $discountSummaryOriginalValue += $summaryPrice->getOriginalPrice()->getValue();

                }

            }

            if($hasDiscount){

                $price = new DiscountPrice($summaryPriceValue, $summaryPriceCurrency);
                $price
                    ->setOriginalPrice(new Price($summaryPriceValue, $summaryPriceCurrency))
                ;

            } else {

                $price = new Price($summaryPriceValue, $summaryPriceCurrency);

            }

            return $price;

        }

    }

    /**
     * @return \Shop\ShippingBundle\Calculator\ShippingCalculatorResultInterface
     */
    public function calculateShipping(){

        return $this->shippingCalculator->calculate([
            'shopCartCategories' => $this->getCategories(),
            'shopCartSummaryPrice' => $this->getSummaryPrice(),
            'city' => $this->getCustomerCity(),
            'liftType' => $this->getCustomerLiftType(),
            'floor' => $this->getCustomerFloor(),
        ]);

    }

    /**
     * @return ArrayCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return array
     */
    public function getCategoryIds(){
        return $this->getCategories()->getKeys();
    }

    /**
     * @return array
     */
    public function getProposalIds(){

        $proposalIds = array();

        /**
         * @var $shopCartCategory ShopCartCategory
         */
        foreach($this->getCategories() as $shopCartCategory){

            /**
             * @var $shopCartProposal ShopCartProposal
             */
            foreach($shopCartCategory->getProposals() as $shopCartProposal){

                $proposalIds[] = $shopCartProposal->getProposal()->getId();

            }

        }

        return $proposalIds;

    }

    /**
     * @return array
     */
    public function getPriceIds(){

        $priceIds = array();

        /**
         * @var $shopCartCategory ShopCartCategory
         */
        foreach($this->getCategories() as $shopCartCategory){

            /**
             * @var $shopCartProposal ShopCartProposal
             */
            foreach($shopCartCategory->getProposals() as $shopCartProposal){

                /**
                 * @var $shopCartPrice ShopCartPrice
                 */
                foreach($shopCartProposal->getPrices() as $shopCartPrice){

                    $priceIds[] = $shopCartPrice->getPrice()->getId();

                }

            }

        }

        return $priceIds;

    }

    /**
     * @return int
     */
    public function getPricesAmount(){

        $amount = 0;

        /**
         * @var $shopCartCategory ShopCartCategory
         */
        foreach($this->getCategories() as $shopCartCategory){

            /**
             * @var $shopCartProposal ShopCartProposal
             */
            foreach($shopCartCategory->getProposals() as $shopCartProposal){

                /**
                 * @var $shopCartPrice ShopCartPrice
                 */
                foreach($shopCartProposal->getPrices() as $shopCartPrice){

                    $amount += $shopCartPrice->getAmount();

                }

            }

        }

        return $amount;

    }

    /**
     * @param mixed $customerCity
     * @return $this
     */
    public function setCustomerCity($customerCity)
    {
        $this->customerCity = $customerCity;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCustomerCity()
    {
        return $this->customerCity;
    }

    /**
     * @param int|null $customerFloor
     * @return $this
     */
    public function setCustomerFloor($customerFloor)
    {
        $this->customerFloor = $customerFloor;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCustomerFloor()
    {
        return $this->customerFloor;
    }

    /**
     * @param int|null $customerLiftType
     * @return $this
     */
    public function setCustomerLiftType($customerLiftType)
    {
        $this->customerLiftType = $customerLiftType;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCustomerLiftType()
    {
        return $this->customerLiftType;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        $method = 'get' . Inflector::classify($offset);
        return method_exists($this, $method);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $method = 'get' . Inflector::classify($offset);
        if(method_exists($this, $method)){
            return $this->$method();
        }
        return null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {}

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {}

} 