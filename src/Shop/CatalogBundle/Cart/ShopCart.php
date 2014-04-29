<?php
namespace Shop\CatalogBundle\Cart;

use Shop\CatalogBundle\Entity\Price;
use Shop\CatalogBundle\Entity\Proposal;
use Shop\CatalogBundle\Entity\ProposalRepository;
use Shop\CatalogBundle\Entity\CategoryRepository;
use Doctrine\ORM\EntityRepository;
use Shop\CatalogBundle\Converter\ShopPriceCurrencyConverter;
use Weasty\CatalogBundle\Data\CategoryInterface;

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
     * @param $storageData
     * @return ShopCartSummary
     */
    public function getSummary($storageData){

        $shopCartSummary = new ShopCartSummary();

        if(isset($storageData['categories']) && is_array($storageData['categories'])){

            $categories = $storageData['categories'];

            foreach($categories as $categoryId => $categoryData){

                if(isset($categoryData['proposalPrices']) && is_array($categoryData['proposalPrices'])){

                    $proposalPrices = array_filter($categoryData['proposalPrices']);
                    if(!$proposalPrices){
                        continue;
                    }

                    $categoryId = (int)$categoryId;
                    $summaryCategory = $shopCartSummary->getCategories()->get($categoryId);

                    if(!$summaryCategory instanceof ShopCartSummaryCategory){

                        $category = $this->categoryRepository->findOneBy(array(
                            'id' => $categoryId,
                        ));

                        if($category instanceof CategoryInterface){

                            $summaryCategory = new ShopCartSummaryCategory($category);
                            $shopCartSummary->getCategories()->set($category->getId(), $summaryCategory);

                        }

                    }

                    if(!$summaryCategory instanceof ShopCartSummaryCategory){
                        continue;
                    }

                    foreach($proposalPrices as $proposalPriceData){

                        if(isset($proposalPriceData['id']) && isset($proposalPriceData['proposalId']) && isset($proposalPriceData['amount'])){

                            $proposalPriceAmount = floatval($proposalPriceData['amount']);

                            if($proposalPriceAmount > 0){

                                $priceId = (int)$proposalPriceData['id'];
                                $proposalId = (int)$proposalPriceData['proposalId'];

                                $summaryProposal = $summaryCategory->getProposals()->get($proposalId);

                                if(!$summaryProposal instanceof ShopCartSummaryProposal){

                                    $proposal = $this->proposalRepository->findOneBy(array(
                                        'id' => $proposalId,
                                    ));

                                    if($proposal instanceof Proposal){

                                        $summaryProposal = new ShopCartSummaryProposal($proposal);
                                        $summaryCategory->getProposals()->set($proposal->getId(), $summaryProposal);

                                    }

                                }

                                if(!$summaryProposal instanceof ShopCartSummaryProposal){
                                    continue;
                                }

                                $summaryPrice = $summaryProposal->getPrices()->get($priceId);

                                if(!$summaryPrice instanceof ShopCartSummaryPrice) {

                                    $proposalPrice = $this->priceRepository->findOneBy(array(
                                        'id' => $priceId,
                                    ));

                                    if ($proposalPrice instanceof Price) {

                                        $summaryPrice = new ShopCartSummaryPrice($proposalPrice);
                                        $summaryProposal->getPrices()->set($proposalPrice->getId(), $summaryPrice);

                                    }

                                }

                                if(!$summaryPrice instanceof ShopCartSummaryPrice){
                                    continue;
                                }

                                $summaryPrice
                                    ->setAmount($proposalPriceAmount)
                                    ->getItemPrice()
                                        ->setValue($this->currencyConverter->convert($summaryPrice->getPrice()))
                                        ->setCurrency($this->currencyConverter->getCurrencyResource()->getDefaultCurrency())
                                ;

                            }

                        }

                    }

                }

            }

        }

        return $shopCartSummary;

    }

} 