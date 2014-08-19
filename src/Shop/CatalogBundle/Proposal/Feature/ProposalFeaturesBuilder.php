<?php
namespace Shop\CatalogBundle\Proposal\Feature;

use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\Proposal;
use Weasty\Bundle\CatalogBundle\Feature\Feature;
use Weasty\Bundle\CatalogBundle\Feature\FeatureGroup;
use Weasty\Bundle\CatalogBundle\Feature\FeaturesResource;

/**
 * Class ProposalFeaturesBuilder
 * @package Shop\CatalogBundle\Proposal\Feature
 */
class ProposalFeaturesBuilder {

    /**
     * @TODO store in cache $proposalFeaturesResource using price::$id in cache key
     * @param Proposal $proposal
     * @param Price $price
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeaturesResourceInterface
     */
    public function build(Proposal $proposal, Price $price = null){

        $proposalFeaturesResource = new FeaturesResource();

        $category = $proposal->getCategory();
        if(!$price){
            $price = $proposal->getPrice();
        }

        if(!$price instanceof Price){
            return $proposalFeaturesResource;
        }

        /**
         * @var $parameterValuesIndexedByParameterId \Shop\CatalogBundle\Entity\ParameterValue[]
         */
        $parameterValuesIndexedByParameterId = [];
        foreach($price->getParameterValues() as $parameterValue){
            $parameterValuesIndexedByParameterId[$parameterValue->getParameterId()] = $parameterValue;
        }

        /**
         * @var $categoryParameterGroup \Shop\CatalogBundle\Entity\CategoryParameterGroup
         * @var $categoryParameter \Shop\CatalogBundle\Entity\CategoryParameter
         */
        foreach($category->getParameterGroups() as $categoryParameterGroup){

            $featureGroup = new FeatureGroup();
            $featureGroup
                ->setId($categoryParameterGroup->getId())
                ->setName($categoryParameterGroup->getName())
            ;

            foreach($categoryParameterGroup->getParameters() as $categoryParameter){

                if(isset($parameterValuesIndexedByParameterId[$categoryParameter->getParameterId()])){

                    $parameterValue = $parameterValuesIndexedByParameterId[$categoryParameter->getParameterId()];

                    $feature = new Feature();
                    $feature
                        ->setId($parameterValue->getParameterId())
                        ->setName($categoryParameter->getName())
                        ->setValue($parameterValue->getOption()->getName())
                    ;

                    $featureGroup->addFeature($feature);

                }

            }

            if($featureGroup->getFeatures()){
                $proposalFeaturesResource->addGroup($featureGroup);
            }

        }

        foreach($category->getParameters() as $categoryParameter){

            if(isset($parameterValuesIndexedByParameterId[$categoryParameter->getParameterId()])){

                $parameterValue = $parameterValuesIndexedByParameterId[$categoryParameter->getParameterId()];

                $feature = new Feature();
                $feature
                    ->setId($parameterValue->getParameterId())
                    ->setName($categoryParameter->getName())
                    ->setValue($parameterValue->getOption()->getName())
                ;

                $proposalFeaturesResource->addFeature($feature);

            }

        }

        return $proposalFeaturesResource;

    }

} 