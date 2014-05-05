<?php
namespace Shop\CatalogBundle\Mapper;

use Shop\CatalogBundle\Entity\Parameter;
use Shop\CatalogBundle\Entity\ParameterOption;
use Shop\CatalogBundle\Entity\ParameterValue;
use Shop\CatalogBundle\Entity\Price;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class PriceParameterValuesMapper
 * @package Shop\CatalogBundle\Mapper
 */
class PriceParameterValuesMapper {

    /**
     * @var Price
     */
    protected $price;

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @param ObjectManager|object $em
     * @param Price $price
     */
    function __construct(ObjectManager $em, Price $price)
    {
        $this->em = $em;
        $this->price = $price;
    }

    /**
     * @param array $parameterValuesData
     * @param bool $removeEmptyValue
     * @return Price
     */
    public function mapParameterValues(array $parameterValuesData = array(), $removeEmptyValue = true){

        $price = $this->getPrice();
        $em = $this->getEm();

        $parametersRepository = $this->getEm()->getRepository('ShopCatalogBundle:Parameter');
        $optionsRepository = $this->getEm()->getRepository('ShopCatalogBundle:ParameterOption');

        /**
         * @var $parameterValue ParameterValue
         */
        foreach($price->getParameterValues() as $parameterValue){

            if(isset($parameterValuesData[$parameterValue ->getParameterId()])){

                $parameterValueData = $parameterValuesData[$parameterValue->getParameterId()];

                if(is_array($parameterValueData)){

                    $optionKey = array_search($parameterValue->getOptionId(), $parameterValueData);
                    $option = $optionKey !== false ? $parameterValue->getOptionId() : null;

                    if($optionKey !== false){
                        unset($parameterValuesData[$parameterValue->getParameterId()][$optionKey]);
                    }

                    if(!$parameterValuesData[$parameterValue->getParameterId()]){
                        unset($parameterValuesData[$parameterValue->getParameterId()]);
                    }

                } else {

                    $option = $parameterValueData;
                    unset($parameterValuesData[$parameterValue->getParameterId()]);

                }

                $parameterOption = null;

                if($option instanceof ParameterOption){

                    $parameterOption = $option;

                } elseif(is_numeric($option)) {

                    $parameterOption = $option ? $optionsRepository->findOneBy(array(
                        'id' => (int)$option,
                    )) : null;

                }

                if($parameterOption instanceof ParameterOption){

                    $parameterValue->setOption($parameterOption);

                } elseif($removeEmptyValue) {

                    $price->removeParameterValue($parameterValue);
                    $em->remove($parameterValue);

                }

            } elseif($removeEmptyValue){

                $price->removeParameterValue($parameterValue);
                $em->remove($parameterValue);

            }

        }

        if($parameterValuesData){

            foreach($parameterValuesData as $parameterId => $parameterValueData){

                if(!is_array($parameterValueData)){

                    $parameterValueData = array(
                        $parameterValueData,
                    );

                }

                foreach($parameterValueData as $option){

                    $parameterOption = null;

                    if($option instanceof ParameterOption){

                        $parameterOption = $option;

                    } elseif(is_numeric($option)) {

                        $parameterOption = $option ? $optionsRepository->findOneBy(array(
                            'id' => (int)$option,
                        )) : null;

                    }

                    if($parameterOption instanceof ParameterOption){

                        $parameter = $parametersRepository->findOneBy(array(
                            'id' => (int)$parameterId,
                        ));

                        if($parameter instanceof Parameter){

                            $parameterValue = new ParameterValue();
                            $parameterValue
                                ->setParameter($parameter)
                                ->setOption($parameterOption);

                            $price->addParameterValue($parameterValue);

                        }

                    }

                }

            }

        }

        return $price;

    }

    /**
     * @return ObjectManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @return \Shop\CatalogBundle\Entity\Price
     */
    public function getPrice()
    {
        return $this->price;
    }

} 