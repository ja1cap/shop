<?php
namespace Shop\CatalogBundle\Cart;

use Doctrine\Common\Inflector\Inflector;
use Weasty\Bundle\CatalogBundle\Proposal\Price\ProposalPriceInterface;
use Weasty\Money\Price\Price;
use Weasty\Money\Price\PriceInterface;

/**
 * Class ShopCartPrice
 * @package Shop\CatalogBundle\Cart
 */
class ShopCartPrice implements PriceInterface, \ArrayAccess {

    /**
     * @var \Shop\CatalogBundle\Entity\Price
     */
    protected $price;

    /**
     * @var \Weasty\Money\Price\PriceInterface
     */
    protected $itemPrice;

    /**
     * @var \Shop\DiscountBundle\Price\DiscountPriceInterface
     */
    protected $itemDiscountPrice;

    /**
     * @var int|float|null
     */
    protected $amount;

    /**
     * @var \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditions|null
     */
    protected $proposalActionConditions;

    function __construct(ProposalPriceInterface $price)
    {
        $this->price = $price;
        $this->amount = 0;
    }

    /**
     * @return integer|float|string
     */
    public function getValue()
    {
        return $this->getItemPrice()->getValue();
    }

    /**
     * @return integer|string|\Weasty\Money\Currency\CurrencyInterface
     */
    public function getCurrency()
    {
        return $this->getItemPrice()->getCurrency();
    }

    /**
     * @return float|int|null
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float|int|null $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param \Weasty\Money\Price\PriceInterface $itemPrice
     * @return $this
     */
    public function setItemPrice($itemPrice)
    {

        $this->itemPrice = $itemPrice;
        return $this;

    }

    /**
     * @return \Weasty\Money\Price\PriceInterface
     */
    public function getItemPrice()
    {

        if(!$this->itemPrice){

            $itemPrice = new Price();
            $itemPrice
                ->setValue($this->getPrice()->getValue())
                ->setCurrency($this->getPrice()->getCurrency())
            ;

            $this->itemPrice = $itemPrice;

        }

        return $this->itemPrice;
    }

    /**
     * @return $this
     */
    public function calculateItemDiscountPrice(){

        $itemPrice = $this->getItemPrice();

        if($this->getProposalActionConditions() && $itemPrice instanceof PriceInterface){
            $this->itemDiscountPrice = $this->getProposalActionConditions()->getDiscountPrice($itemPrice);
        }

        return $this;

    }

    /**
     * @return \Shop\DiscountBundle\Price\DiscountPriceInterface
     */
    public function getItemDiscountPrice()
    {
        return $this->itemDiscountPrice;
    }

    /**
     * @return \Shop\DiscountBundle\Price\DiscountPriceInterface
     */
    public function getDiscountPrice(){
        return $this->getItemDiscountPrice();
    }

    /**
     * @return \Weasty\Bundle\CatalogBundle\Proposal\Price\ProposalPriceInterface
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return \Weasty\Money\Price\PriceInterface|\Shop\DiscountBundle\Price\DiscountPriceInterface
     */
    public function getSummaryPrice()
    {

        if($this->getAmount() > 1){

            $price = new Price($this->getItemPrice()->getValue() * $this->getAmount(), $this->getItemPrice()->getCurrency());

            if($this->getProposalActionConditions()){

                $discountPrice = $this->getProposalActionConditions()->getDiscountPrice($this->getItemPrice());
                if($discountPrice){

                    $discountPrice
                        ->setValue($discountPrice->getValue() * $this->getAmount())
                        ->setOriginalPrice($price)
                    ;

                    $price = $discountPrice;

                }

            }


        } else {

            $price = $this->getDiscountPrice() ?: $this->getItemPrice();

        }

        return $price;

    }

    /**
     * @return \Weasty\Money\Price\PriceInterface
     */
    public function getSummary()
    {
        return $this->getSummaryPrice();
    }

    /**
     * @return null|\Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditions
     */
    public function getProposalActionConditions()
    {
        return $this->proposalActionConditions;
    }

    /**
     * @param null|\Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditions $proposalActionConditions
     * @return $this
     */
    public function setProposalActionConditions($proposalActionConditions)
    {
        $this->proposalActionConditions = $proposalActionConditions;
        return $this;
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