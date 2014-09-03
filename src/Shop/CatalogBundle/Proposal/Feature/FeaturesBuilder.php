<?php
namespace Shop\CatalogBundle\Proposal\Feature;

use Shop\CatalogBundle\Category\CategoryInterface;
use Shop\CatalogBundle\Category\Parameter\CategoryParameterInterface;
use Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface;
use Weasty\Bundle\CatalogBundle\Feature\Feature;
use Weasty\Bundle\CatalogBundle\Feature\FeatureGroup;
use Weasty\Bundle\CatalogBundle\Feature\FeatureInterface;
use Weasty\Bundle\CatalogBundle\Feature\FeaturesResource;
use Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface;
use Weasty\Bundle\CatalogBundle\Proposal\Feature\ProposalFeatureValue;
use Weasty\Bundle\CatalogBundle\Proposal\Feature\ProposalFeatureValueInterface;

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
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeatureInterface
     */
    protected function buildFeature(CategoryParameterInterface $categoryParameter){

        $feature = $this->createFeature();

        $feature
            ->setId($categoryParameter->getParameterId())
            ->setName($categoryParameter->getName())
        ;

        return $feature;

    }

    /**
     * @return \Weasty\Bundle\CatalogBundle\Proposal\Feature\ProposalFeatureValueInterface
     */
    protected function createFeatureValue(){
        return new ProposalFeatureValue();
    }

    /**
     * @param \Weasty\Bundle\CatalogBundle\Feature\FeatureInterface $feature
     * @param \Shop\CatalogBundle\Proposal\Price\ProposalPriceInterface $price
     * @param \Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface $parameterValue
     * @return ProposalFeatureValueInterface
     */
    protected function buildFeatureValue(FeatureInterface $feature, ProposalPriceInterface $price, ParameterValueInterface $parameterValue){

        $featureValue = $this->createFeatureValue();

        $featureValue
            ->setPriceId($price->getId())
            ->setProposalId($price->getProposalId())
            ->setValue($parameterValue->getOption()->getName())
        ;

        $featureValue->setFeature($feature);

        return $featureValue;

    }

    /**
     * @param CategoryInterface $category
     * @param ProposalPriceInterface[] $prices
     * @return \Weasty\Bundle\CatalogBundle\Feature\FeaturesResourceInterface
     */
    public function build(CategoryInterface $category, $prices = []){

        $proposalFeaturesResource = new FeaturesResource();

        /**
         * @var \Weasty\Bundle\CatalogBundle\Feature\FeatureInterface[] $features
         */
        $features = [];

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

                $feature = $this->buildFeature($categoryParameter);
                $features[$feature->getId()] = $feature;

                $featureGroup->addFeature($feature);

            }

            if($featureGroup->getFeatures()){
                $proposalFeaturesResource->addGroup($featureGroup);
            }

        }

        foreach($category->getParameters() as $categoryParameter){

            if(!$categoryParameter->getGroupId()){

                $feature = $this->buildFeature($categoryParameter);
                $features[$feature->getId()] = $feature;

                $proposalFeaturesResource->addFeature($feature);

            }

        }

        foreach($prices as $price){

            if($price instanceof ProposalPriceInterface){

                /**
                 * @var \Weasty\Bundle\CatalogBundle\Parameter\Value\ParameterValueInterface $parameterValue
                 */
                foreach($price->getParameterValues() as $parameterValue){

                    if(isset($features[$parameterValue->getParameterId()])){

                        $feature = $features[$parameterValue->getParameterId()];

                        $featureValue = $this->buildFeatureValue($feature, $price, $parameterValue);
                        $feature->addFeatureValue($featureValue->getPriceId(), $featureValue);

                    }

                }

            }

        }

        return $proposalFeaturesResource;

    }

} 