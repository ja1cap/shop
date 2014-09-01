<?php
namespace Shop\CatalogBundle\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Inflector;
use Shop\DiscountBundle\Price\DiscountPrice;
use Shop\DiscountBundle\Price\DiscountPriceInterface;
use Weasty\Bundle\CatalogBundle\Category\CategoryInterface;
use Weasty\Money\Price\Price;

/**
 * Class ShopCartCategory
 * @package Shop\CatalogBundle\Cart
 */
class ShopCartCategory implements \ArrayAccess {

    /**
     * @var \Weasty\Bundle\CatalogBundle\Category\CategoryInterface
     */
    protected $category;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $proposals;

    function __construct(CategoryInterface $category)
    {
        $this->category = $category;
        $this->proposals = new ArrayCollection();
    }

    /**
     * @return \Weasty\Bundle\CatalogBundle\Category\CategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return ArrayCollection
     */
    public function getProposals()
    {
        return $this->proposals;
    }

    /**
     * @return \Weasty\Money\Price\PriceInterface|\Shop\DiscountBundle\Price\DiscountPriceInterface|null
     */
    public function getSummaryPrice(){

        if($this->getProposals()->count() == 1){

            $shopCartProposal = $this->getProposals()->current();
            if($shopCartProposal instanceof ShopCartProposal){

                return $shopCartProposal->getSummary();

            } else {

                return null;

            }

        } else {

            $summaryPriceValue = 0;
            $summaryPriceCurrency = null;

            $hasDiscount = false;
            $discountSummaryOriginalValue = 0;

            foreach($this->getProposals() as $shopCartProposal){

                $summaryPrice = $shopCartProposal->getSummaryPrice();
                $summaryPriceValue += $shopCartProposal->getValue();
                $summaryPriceCurrency = $shopCartProposal->getCurrency();

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