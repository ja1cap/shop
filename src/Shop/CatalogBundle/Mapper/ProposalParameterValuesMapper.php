<?php
namespace Shop\CatalogBundle\Mapper;

use Shop\CatalogBundle\Entity\Parameter;
use Shop\CatalogBundle\Entity\ParameterOption;
use Shop\CatalogBundle\Entity\ParameterValue;
use Shop\CatalogBundle\Entity\Proposal;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ProposalParameterValuesMapper
 * @package Shop\CatalogBundle\Mapper
 */
class ProposalParameterValuesMapper {

    /**
     * @var Proposal
     */
    protected $proposal;

    /**
     * @var ObjectManager
     */
    protected $em;

    /**
     * @param ObjectManager|object $em
     * @param Proposal $proposal
     */
    function __construct(ObjectManager $em, Proposal $proposal)
    {
        $this->em = $em;
        $this->proposal = $proposal;
    }

    /**
     * @param array $parameterValuesData
     * @param bool $removeEmptyValue
     * @return Proposal
     */
    public function mapParameterValues(array $parameterValuesData = array(), $removeEmptyValue = true){

        $proposal = $this->getProposal();
        $em = $this->getEm();

        $parametersRepository = $this->getEm()->getRepository('ShopCatalogBundle:Parameter');
        $optionsRepository = $this->getEm()->getRepository('ShopCatalogBundle:ParameterOption');

        /**
         * @var $parameterValue ParameterValue
         */
        foreach($proposal->getParameterValues() as $parameterValue){

            if(isset($parameterValuesData[$parameterValue ->getParameterId()])){

                $option = $parameterValuesData[$parameterValue->getParameterId()];
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

                    $proposal->removeParameterValue($parameterValue);
                    $em->remove($parameterValue);

                }

                unset($parameterValuesData[$parameterValue->getParameterId()]);

            }

        }

        if($parameterValuesData){

            foreach($parameterValuesData as $parameterId => $option){

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

                        $proposal->addParameterValue($parameterValue);

                    }

                }

            }

        }

        return $proposal;

    }

    /**
     * @return ObjectManager
     */
    public function getEm()
    {
        return $this->em;
    }

    /**
     * @return \Shop\CatalogBundle\Entity\Proposal
     */
    public function getProposal()
    {
        return $this->proposal;
    }

} 