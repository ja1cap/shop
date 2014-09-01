<?php
namespace Shop\CatalogBundle\Proposal\Feature;

use Shop\CatalogBundle\Category\Parameter\CategoryParameterInterface;
use Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface;
use Shop\CatalogBundle\Proposal\ProposalInterface;
use Weasty\Bundle\CatalogBundle\Feature\Feature;
use Weasty\Bundle\CatalogBundle\Feature\FeatureGroup;
use Weasty\Bundle\CatalogBundle\Feature\FeaturesResource;
use Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface;

/**
 * Class FeaturesBuilder
 * @package Shop\CatalogBundle\Proposal\Feature
 */
class FeaturesBuilder {

    /**
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeatureInterface
     */
    protected function createFeature(){
        return new Feature();
    }

    /**
     * @param CategoryParameterInterface $categoryParameter
     * @param ParameterValueInterface|null $parameterValue
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeatureInterface
     */
    protected function buildFeature(CategoryParameterInterface $categoryParameter, ParameterValueInterface $parameterValue = null){

        $feature = $this->createFeature();

        $feature
            ->setId($categoryParameter->getParameterId())
            ->setName($categoryParameter->getName())
        ;

        if($parameterValue){

            $feature
                ->setValue($parameterValue->getOption()->getName())
                ->setWeight($parameterValue->getOption()->getPosition())
            ;

        }

        return $feature;

    }

    /**
     * @param ProposalInterface $proposal
     * @param ProposalPriceInterface $price
     * @param bool $includeNotDefinedParameters
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeaturesResourceInterface
     */
    public function build(ProposalInterface $proposal, ProposalPriceInterface $price, $includeNotDefinedParameters = false){

        $proposalFeaturesResource = new FeaturesResource();
        $category = $proposal->getCategory();
        if(!$price instanceof ProposalPriceInterface){
            return $proposalFeaturesResource;
        }

        /**
         * @var $parameterValuesIndexedByParameterId \Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface[]
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

                $parameterValue = null;
                if(isset($parameterValuesIndexedByParameterId[$categoryParameter->getParameterId()])){
                    $parameterValue = $parameterValuesIndexedByParameterId[$categoryParameter->getParameterId()];
                }

                if($parameterValue || $includeNotDefinedParameters){

                    $feature = $this->buildFeature($categoryParameter, $parameterValue);
                    $featureGroup->addFeature($feature);

                }

            }

            if($featureGroup->getFeatures()){
                $proposalFeaturesResource->addGroup($featureGroup);
            }

        }

        foreach($category->getParameters() as $categoryParameter){

            $parameterValue = null;
            if(isset($parameterValuesIndexedByParameterId[$categoryParameter->getParameterId()])){
                $parameterValue = $parameterValuesIndexedByParameterId[$categoryParameter->getParameterId()];
            }

            if($parameterValue || $includeNotDefinedParameters){

                $feature = $this->buildFeature($categoryParameter, $parameterValue);
                $proposalFeaturesResource->addFeature($feature);

            }

        }

        return $proposalFeaturesResource;

    }

} 