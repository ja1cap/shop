<?php
namespace Shop\MainBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Class ProposalRepository
 * @package Shop\MainBundle\Entity
 */
class ProposalRepository extends EntityRepository {

    /**
     * @return array
     */
    public function getTypes(){

        /**
         * @var $proposalMetadata \Doctrine\ORM\Mapping\ClassMetadata
         */
        $proposalMetadata = $this->getClassMetadata();
        $proposalTypes = array();

        foreach($proposalMetadata->discriminatorMap as $discriminatorValue => $subClassName){

            $proposalTypeSingleName = constant($subClassName . '::PROPOSAL_TYPE_SINGLE_NAME');
            $proposalTypeMultipleName = constant($subClassName . '::PROPOSAL_TYPE_MULTIPLE_NAME');

            $proposalType = array(
                'type' => $discriminatorValue,
                'class_name' => $subClassName,
                'single_name' => $proposalTypeSingleName,
                'multiple_name' => $proposalTypeMultipleName,
            );

            $proposalTypes[$discriminatorValue] = $proposalType;

        }

        return $proposalTypes;

    }

} 