<?php
namespace Shop\CatalogBundle\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Inflector;
use Weasty\Bundle\CatalogBundle\Data\ProposalPriceInterface;
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
     * @param ProposalPriceInterface $proposalPrice
     * @return ShopCartPrice|null
     */
    public function getProposalPrice(ProposalPriceInterface $proposalPrice){

        /**
         * @var $shopCartCategory ShopCartCategory
         */
        $shopCartCategory = $this->getCategories()->get($proposalPrice->getCategoryId());
        if(!$shopCartCategory){
            return null;
        }

        /**
         * @var $shopCartProposal ShopCartProposal
         */
        $shopCartProposal = $shopCartCategory->getProposals()->get($proposalPrice->getProposalId());
        if(!$shopCartProposal){
            return null;
        }

        return $shopCartProposal->getPrices()->get($proposalPrice->getId());

    }

    /**
     * @param ProposalPriceInterface $proposalPrice
     * @param int $amount
     * @return $this
     */
    public function addProposalPrice(ProposalPriceInterface $proposalPrice, $amount = 1){

        /**
         * @var $shopCartCategory ShopCartCategory
         */
        $shopCartCategory = $this->getCategories()->get($proposalPrice->getCategoryId());
        if(!$shopCartCategory){
            $shopCartCategory = new ShopCartCategory($proposalPrice->getCategory());
            $this->getCategories()->set($proposalPrice->getCategoryId(), $shopCartCategory);
        }

        /**
         * @var $shopCartProposal ShopCartProposal
         */
        $shopCartProposal = $shopCartCategory->getProposals()->get($proposalPrice->getProposalId());
        if(!$shopCartProposal){
            $shopCartProposal = new ShopCartProposal($proposalPrice->getProposal());
            $shopCartCategory->getProposals()->set($proposalPrice->getProposalId(), $shopCartProposal);
        }

        /**
         * @var $shopCartPrice ShopCartPrice
         */
        $shopCartPrice = new ShopCartPrice($proposalPrice);

        $shopCartPrice
            ->setAmount($amount)
            ->getItemPrice()
                ->setValue($this->currencyConverter->convert($shopCartPrice->getPrice()))
                ->setCurrency($this->currencyConverter->getCurrencyResource()->getDefaultCurrency())
        ;

        $shopCartProposal->getPrices()->set($proposalPrice->getId(), $shopCartPrice);

        return $this;

    }

    /**
     * @return \Weasty\Money\Price\Price
     */
    public function getSummaryPrice(){

        $summaryPriceValue = 0;
        $summaryPriceCurrency = null;

        /**
         * @var $shopCartCategory ShopCartCategory
         */
        foreach($this->getCategories() as $shopCartCategory){

            $summaryPrice = $shopCartCategory->getSummaryPrice();
            $summaryPriceValue += $summaryPrice->getValue();
            $summaryPriceCurrency = $summaryPrice->getCurrency();

        }

        $summaryPrice = new Price($summaryPriceValue, $summaryPriceCurrency);

        return $summaryPrice;

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
         * @var $summaryCategory ShopCartCategory
         */
        foreach($this->getCategories() as $summaryCategory){

            /**
             * @var $summaryProposal ShopCartProposal
             */
            foreach($summaryCategory->getProposals() as $summaryProposal){

                $proposalIds[] = $summaryProposal->getProposal()->getId();

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
         * @var $summaryCategory ShopCartCategory
         */
        foreach($this->getCategories() as $summaryCategory){

            /**
             * @var $summaryProposal ShopCartProposal
             */
            foreach($summaryCategory->getProposals() as $summaryProposal){

                /**
                 * @var $summaryPrice ShopCartPrice
                 */
                foreach($summaryProposal->getPrices() as $summaryPrice){

                    $priceIds[] = $summaryPrice->getPrice()->getId();

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
         * @var $summaryCategory ShopCartCategory
         */
        foreach($this->getCategories() as $summaryCategory){

            /**
             * @var $summaryProposal ShopCartProposal
             */
            foreach($summaryCategory->getProposals() as $summaryProposal){

                /**
                 * @var $summaryPrice ShopCartPrice
                 */
                foreach($summaryProposal->getPrices() as $summaryPrice){

                    $amount += $summaryPrice->getAmount();

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