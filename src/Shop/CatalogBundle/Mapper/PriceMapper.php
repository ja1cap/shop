<?php
namespace Shop\CatalogBundle\Mapper;

use Weasty\Doctrine\Mapper\AbstractEntityMapper;

/**
 * Class PriceMapper
 * @package Shop\CatalogBundle\Mapper
 */
class PriceMapper extends AbstractEntityMapper {

    /**
     * @return array
     */
    public function getPrice(){
        return array(
            'value' => $this->getEntity()->getValue(),
            'currency' => $this->getEntity()->getCurrencyNumericCode(),
        );
    }

    /**
     * @param $data
     * @return $this
     */
    public function setPrice($data){

        $this->getEntity()
            ->setValue($data['value'])
            ->setCurrencyNumericCode($data['currency'])
        ;

        return $this;

    }

    /**
     * @return \Shop\CatalogBundle\Entity\Price
     */
    public function getEntity()
    {
        return parent::getEntity();
    }

} 