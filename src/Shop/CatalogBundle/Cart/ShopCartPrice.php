<?php
namespace Shop\CatalogBundle\Cart;

use Doctrine\Common\Inflector\Inflector;
use Weasty\CatalogBundle\Data\ProposalPriceInterface;
use Weasty\MoneyBundle\Data\Price;
use Weasty\MoneyBundle\Data\PriceInterface;

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
     * @var \Weasty\MoneyBundle\Data\Price
     */
    protected $itemPrice;

    /**
     * @var int|float|null
     */
    protected $amount;

    function __construct(ProposalPriceInterface $price)
    {
        $this->price = $price;
        $this->amount = 0;
        $this->itemPrice = new Price();
    }

    /**
     * @return integer|float|string
     */
    public function getValue()
    {
        return $this->getItemPrice()->getValue();
    }

    /**
     * @return integer|string|\Weasty\MoneyBundle\Data\CurrencyInterface
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
     * @return Price
     */
    public function getItemPrice()
    {
        return $this->itemPrice;
    }

    /**
     * @return ProposalPriceInterface
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return Price
     */
    public function getSummaryPrice()
    {
        return new Price($this->getValue() * $this->getAmount(), $this->getCurrency());
    }

    /**
     * @return Price
     */
    public function getSummary()
    {
        return $this->getSummaryPrice();
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