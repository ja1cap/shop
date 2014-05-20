<?php
namespace Shop\CatalogBundle\Cart;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Util\Inflector;
use Weasty\MoneyBundle\Data\Price;
use Weasty\CatalogBundle\Data\ProposalInterface;

/**
 * Class ShopCartProposalSummary
 * @package Shop\CatalogBundle\Cart
 */
class ShopCartProposal implements \ArrayAccess {

    /**
     * @var \Shop\CatalogBundle\Entity\Proposal
     */
    protected $proposal;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $prices;

    /**
     * @param ProposalInterface $proposal
     */
    function __construct(ProposalInterface $proposal)
    {
        $this->proposal = $proposal;
        $this->prices = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @return ProposalInterface
     */
    public function getProposal()
    {
        return $this->proposal;
    }

    /**
     * @return Price
     */
    public function getSummaryPrice(){

        $proposalSummaryPriceValue = 0;
        $proposalSummaryPriceCurrency = null;

        /**
         * @var $shopCartPrice ShopCartPrice
         */
        foreach($this->getPrices() as $shopCartPrice){

            $summaryPrice = $shopCartPrice->getSummaryPrice();

            $proposalSummaryPriceValue += $summaryPrice->getValue();
            $proposalSummaryPriceCurrency = $summaryPrice->getCurrency();

        }

        return new Price($proposalSummaryPriceValue, $proposalSummaryPriceCurrency);

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