<?php
namespace Shop\CatalogBundle\Proposal\Price;

use Shop\CatalogBundle\Entity\Price;
use Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface;
use Weasty\Doctrine\Cache\Collection\CacheCollection;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Class PriceElement
 * @package Shop\CatalogBundle\Proposal\Price
 */
class PriceElement extends CacheCollectionElement
    implements ProposalPriceInterface
{

    /**
     * @var array
     */
    public $parameterValueIds = [];

    /**
     * @var \Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface[]
     */
    private $parameterValues = [];

    /**
     * @var \Shop\CatalogBundle\Contractor\ContractorInterface
     */
    private $contractor;

    /**
     * @param CacheCollection $collection
     * @param EntityInterface $entity
     * @return array
     */
    protected function buildData(CacheCollection $collection, EntityInterface $entity)
    {

        $data = parent::buildData($collection, $entity);

        $entity = $this->getEntity();
        if($entity instanceof Price){

            $parameterValueCollection = $collection->getCollectionManager()->getCollection('ShopCatalogBundle:ParameterValue');

            $parameterValues = $entity->getParameterValues();
            foreach($parameterValues as $parameterValue){

                $parameterValueElement = $parameterValueCollection->saveElement($parameterValue);
                if(!$parameterValueElement){
                    continue;
                }

                $this->parameterValueIds[] = $parameterValue->getId();
                $this->parameterValues[] = $parameterValueElement;

            }

        }

        return $data;

    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * @return integer|float|string
     */
    public function getValue()
    {
        return $this->data['value'];
    }

    /**
     * @return integer|string|\Weasty\Money\Currency\CurrencyInterface
     */
    public function getCurrency()
    {
        return $this->getCurrencyNumericCode();
    }

    /**
     * Get currencyNumericCode
     *
     * @return integer
     */
    public function getCurrencyNumericCode()
    {
        return $this->data['currencyNumericCode'];
    }

    /**
     * Get contractorId
     *
     * @return integer
     */
    public function getContractorId()
    {
        return $this->data['contractorId'];
    }

    /**
     * @return \Shop\CatalogBundle\Contractor\ContractorInterface
     */
    public function getContractor()
    {
        if(!$this->contractor){
            $this->contractor = $this->getCollectionManager()->getCollection('ShopCatalogBundle:Contractor')->get($this->getContractorId());
        }
        return $this->contractor;
    }

    /**
     * @return integer
     */
    public function getProposalId()
    {
        return $this->data['proposalId'];
    }

    /**
     * @return \Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface
     */
    public function getProposal()
    {
        return $this->getCollectionManager()->getCollection('ShopCatalogBundle:Proposal')->get($this->getProposalId());
    }

    /**
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->data['categoryId'];
    }

    /**
     * @return \Weasty\Bundle\CatalogBundle\Category\CategoryInterface
     */
    public function getCategory()
    {
        return $this->getCollectionManager()->getCollection('ShopCatalogBundle:Category')->get($this->getCategoryId());
    }

    /**
     * Get parameterValues
     *
     * @return \Doctrine\Common\Collections\Collection|\Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface[]
     */
    public function getParameterValues()
    {

        if(!$this->parameterValues && $this->parameterValueIds){

            $parameterValueCollection = $this->getCollectionManager()->getCollection('ShopCatalogBundle:ParameterValue');

            foreach($this->parameterValueIds as $parameterValueId){

                $parameterValue = $parameterValueCollection->get($parameterValueId);

                if($parameterValue instanceof ParameterValueInterface){
                    $this->parameterValues[] = $parameterValue;
                }

            }

        }

        return $this->parameterValues;

    }

}