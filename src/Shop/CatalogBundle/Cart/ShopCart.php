<?php
namespace Shop\CatalogBundle\Cart;

use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\ProposalRepository;
use Shop\CatalogBundle\Entity\CategoryRepository;
use Doctrine\ORM\EntityRepository;

/**
 * Class ShopCart
 * @package Shop\CatalogBundle\Cart
 */
class ShopCart {

    /**
     * @var array|null
     */
    protected $storageData;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var ProposalRepository
     */
    protected $proposalRepository;

    /**
     * @var EntityRepository
     */
    protected $priceRepository;

    /**
     * @param $categoryRepository
     * @param $proposalRepository
     * @param $priceRepository
     * @param $storageData
     */
    function __construct($categoryRepository, $proposalRepository, $priceRepository, $storageData)
    {
        $this->categoryRepository = $categoryRepository;
        $this->proposalRepository = $proposalRepository;
        $this->priceRepository = $priceRepository;
        $this->storageData = $storageData;
    }

    /**
     * @return array
     */
    public function getSummary(){

        $proposalIds = array();
        $priceIds = array();
        $categoryProposalsSummary = array();
        $summaryPrice = 0;

        if(isset($this->storageData['categories']) && is_array($this->storageData['categories'])){

            $categories = $this->storageData['categories'];

            foreach($categories as $categoryId => $categoryData){

                if(isset($categoryData['proposalPrices']) && is_array($categoryData['proposalPrices'])){

                    $proposalPrices = array_filter($categoryData['proposalPrices']);

                    if($proposalPrices){

                        $categoryId = (int)$categoryId;

                        if(!isset($categoryProposalsSummary[$categoryId])){

                            $category = $this->categoryRepository->findOneBy(array(
                                'id' => $categoryId,
                            ));

                            $categoryProposalsSummary[$categoryId] = array(
                                'category' => $category,
                                'proposals' => array(),
                            );

                        }

                        foreach($proposalPrices as $proposalPriceData){

                            if(isset($proposalPriceData['id']) && isset($proposalPriceData['proposalId']) && isset($proposalPriceData['amount'])){

                                $amount = floatval($proposalPriceData['amount']);

                                if($amount > 0){

                                    $priceId = (int)$proposalPriceData['id'];
                                    $proposalId = (int)$proposalPriceData['proposalId'];

                                    if(!isset($categoryProposalsSummary[$categoryId]['proposals'][$proposalId])){

                                        $proposal = $this->proposalRepository->findOneBy(array(
                                            'id' => $proposalId,
                                        ));

                                        if($proposal){
                                            $proposalIds[] = $proposalId;
                                        }

                                        $categoryProposalsSummary[$categoryId]['proposals'][$proposalId] = array(
                                            'proposal' => $proposal,
                                            'prices' => array()
                                        );

                                    }

                                    $price = $this->priceRepository->findOneBy(array(
                                        'id' => $priceId,
                                    ));

                                    if($price instanceof Price){

                                        $priceIds[] = $priceId;
                                        $summaryPrice += (floatval($price->getValue()) * $amount);

                                        $categoryProposalsSummary[$categoryId]['proposals'][$proposalId]['prices'][$priceId] = array(
                                            'price' => $price,
                                            'amount' => $amount
                                        );

                                    }

                                }

                            }

                        }

                    }

                }

            }

        }

        return array(
            'proposalIds' => $proposalIds,
            'priceIds' => $priceIds,
            'categoriesIds' => array_keys($categoryProposalsSummary),
            'categories' => $categoryProposalsSummary,
            'summaryPrice' => $summaryPrice,
        );

    }

} 