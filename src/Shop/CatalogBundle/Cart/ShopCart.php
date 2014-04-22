<?php
namespace Shop\CatalogBundle\Cart;

use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\ProposalRepository;
use Shop\CatalogBundle\Entity\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Shop\CatalogBundle\Converter\ShopPriceCurrencyConverter;

/**
 * Class ShopCart
 * @package Shop\CatalogBundle\Cart
 */
class ShopCart {

    /**
     * @var ShopPriceCurrencyConverter
     */
    protected $currencyConverter;

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
     * @param $currencyConverter
     * @param $categoryRepository
     * @param $proposalRepository
     * @param $priceRepository
     */
    function __construct(
        $currencyConverter,
        $categoryRepository,
        $proposalRepository,
        $priceRepository
    )
    {
        $this->currencyConverter = $currencyConverter;
        $this->categoryRepository = $categoryRepository;
        $this->proposalRepository = $proposalRepository;
        $this->priceRepository = $priceRepository;
    }

    /**
     * @param array $storageData
     * @return array
     */
    public function getSummary($storageData){

        $proposalIds = array();
        $priceIds = array();
        $categoryProposalsSummary = array();
        $summaryPrice = 0;

        if(isset($storageData['categories']) && is_array($storageData['categories'])){

            $categories = $storageData['categories'];

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

                                $proposalPriceAmount = floatval($proposalPriceData['amount']);

                                if($proposalPriceAmount > 0){

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

                                    $proposalPrice = $this->priceRepository->findOneBy(array(
                                        'id' => $priceId,
                                    ));

                                    if($proposalPrice instanceof Price){

                                        $priceIds[] = $priceId;

                                        $proposalPriceValue = $this->currencyConverter->convert($proposalPrice);
                                        $proposalPriceSummary = ($this->currencyConverter->convert($proposalPrice) * $proposalPriceAmount);
                                        $summaryPrice += $proposalPriceSummary;

                                        $categoryProposalsSummary[$categoryId]['proposals'][$proposalId]['prices'][$priceId] = array(
                                            'price' => $proposalPrice,
                                            'value' => $proposalPriceValue,
                                            'amount' => $proposalPriceAmount,
                                            'summary' => $proposalPriceSummary,
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
            'categoryIds' => array_keys($categoryProposalsSummary),
            'categories' => $categoryProposalsSummary,
            'summaryPrice' => $summaryPrice,
        );

    }

} 