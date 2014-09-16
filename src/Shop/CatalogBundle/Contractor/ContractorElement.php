<?php
namespace Shop\CatalogBundle\Contractor;

use Shop\CatalogBundle\Contractor\Currency\ContractorCurrencyInterface;
use Shop\CatalogBundle\Entity\Contractor;
use Weasty\Doctrine\Cache\Collection\CacheCollection;
use Weasty\Doctrine\Cache\Collection\CacheCollectionElement;
use Weasty\Doctrine\Entity\EntityInterface;

/**
 * Class ContractorElement
 * @package Shop\CatalogBundle\Contractor
 */
class ContractorElement extends CacheCollectionElement
    implements ContractorInterface
{

    /**
     * @var array
     */
    public $currencyIds = [];

    /**
     * @var \Weasty\Bundle\CatalogBundle\Contractor\Currency\ContractorCurrencyInterface[]
     */
    private $currencies = [];

    /**
     * @param CacheCollection $collection
     * @param EntityInterface $entity
     * @return array
     */
    protected function buildData(CacheCollection $collection, EntityInterface $entity)
    {

        $data = parent::buildData($collection, $entity);

        $entity = $this->getEntity();
        if($entity instanceof Contractor){

            $contractorCurrencyCollection = $collection->getCollectionManager()->getCollection('ShopCatalogBundle:ContractorCurrency');

            $currencies = $entity->getCurrencies();
            foreach($currencies as $currency){

                $currencyElement = $contractorCurrencyCollection->saveElement($currency);
                if(!$currencyElement){
                    continue;
                }

                $this->currencyIds[] = $currency->getId();
                $this->currencies[] = $currencyElement;

            }

        }

        return $data;

    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->data['id'];
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->data['name'];
    }

    /**
     * Get defaultCurrencyNumericCode
     *
     * @return integer
     */
    public function getDefaultCurrencyNumericCode()
    {
        return $this->data['defaultCurrencyNumericCode'];
    }

    /**
     * Get currencies
     *
     * @return \Doctrine\Common\Collections\Collection|\Weasty\Bundle\CatalogBundle\Contractor\Currency\ContractorCurrencyInterface[]
     */
    public function getCurrencies()
    {

        if(!$this->currencies && $this->currencyIds){

            $contractorCurrencyCollection = $this->getCollectionManager()->getCollection('ShopCatalogBundle:ContractorCurrency');

            foreach($this->currencyIds as $currencyId){

                $currency = $contractorCurrencyCollection->get($currencyId);

                if($currency instanceof ContractorCurrencyInterface){
                    $this->currencies[] = $currency;
                }

            }

        }

        return $this->currencies;

    }

} 