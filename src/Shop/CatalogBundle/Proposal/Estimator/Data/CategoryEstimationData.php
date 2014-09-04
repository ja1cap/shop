<?php
namespace Shop\CatalogBundle\Proposal\Estimator\Data;

/**
 * Class CategoryEstimationData
 * @package Shop\CatalogBundle\Proposal\Estimator\Data
 */
class CategoryEstimationData {


    /**
     * @var array
     */
    public $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getCategoryId(){
        return (int)$this->data['id'];
    }

    /**
     * @return array
     */
    public function getProposalPrices(){
        return array_reverse($this->data['proposalPrices']);
    }

} 