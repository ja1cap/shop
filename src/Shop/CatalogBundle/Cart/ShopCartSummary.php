<?php
namespace Shop\CatalogBundle\Cart;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Inflector;
use Shop\ShippingBundle\Calculator\ShippingCalculatorResultInterface;
use Weasty\MoneyBundle\Data\Price;

/**
 * Class ShopCartSummary
 * @package Shop\CatalogBundle\Cart
 */
class ShopCartSummary implements \ArrayAccess {

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $categories;

    /**
     * @var \Shop\ShippingBundle\Calculator\ShippingCalculatorResultInterface
     */
    protected $shippingCalculatorResult;

    function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * @return \Shop\ShippingBundle\Calculator\ShippingCalculatorResultInterface
     */
    public function getShippingCalculatorResult()
    {
        return $this->shippingCalculatorResult;
    }

    /**
     * @param \Shop\ShippingBundle\Calculator\ShippingCalculatorResultInterface $shippingCalculatorResult
     */
    public function setShippingCalculatorResult($shippingCalculatorResult)
    {
        $this->shippingCalculatorResult = $shippingCalculatorResult;
    }

    /**
     * @param array $options
     * @return Price
     */
    public function getSummaryPrice(array $options = array()){

        $optionsCollection = new ArrayCollection(is_array($options) ? $options : array());

        $summaryPriceValue = 0;
        $summaryPriceCurrency = null;

        /**
         * @var $shopCartSummaryCategory ShopCartSummaryCategory
         */
        foreach($this->getCategories() as $shopCartSummaryCategory){

            $summaryPrice = $shopCartSummaryCategory->getSummaryPrice();
            $summaryPriceValue += $summaryPrice->getValue();
            $summaryPriceCurrency = $summaryPrice->getCurrency();

        }

        if($optionsCollection->get('includeShipping')){

            if($this->getShippingCalculatorResult() instanceof ShippingCalculatorResultInterface){

                $summaryPriceValue += $this->getShippingCalculatorResult()->getSummaryPrice()->getValue();

            }

        }

        $summaryPrice = new Price($summaryPriceValue, $summaryPriceCurrency);

        return $summaryPrice;

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
         * @var $summaryCategory ShopCartSummaryCategory
         */
        foreach($this->getCategories() as $summaryCategory){

            /**
             * @var $summaryProposal ShopCartSummaryProposal
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
         * @var $summaryCategory ShopCartSummaryCategory
         */
        foreach($this->getCategories() as $summaryCategory){

            /**
             * @var $summaryProposal ShopCartSummaryProposal
             */
            foreach($summaryCategory->getProposals() as $summaryProposal){

                /**
                 * @var $summaryPrice ShopCartSummaryPrice
                 */
                foreach($summaryProposal->getPrices() as $summaryPrice){

                    $priceIds[] = $summaryPrice->getPrice()->getId();

                }

            }

        }

        return $priceIds;

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