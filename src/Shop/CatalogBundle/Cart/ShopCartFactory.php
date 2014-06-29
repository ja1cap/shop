<?php
namespace Shop\CatalogBundle\Cart;

use Weasty\Bundle\CatalogBundle\Data\CategoryInterface;
use Weasty\Bundle\CatalogBundle\Data\ProposalInterface;
use Weasty\Bundle\CatalogBundle\Data\ProposalPriceInterface;

/**
 * Class ShopCartFactory
 * @package Shop\CatalogBundle\Cart
 */
class ShopCartFactory {

    /**
     * @var \Weasty\Bundle\GeonamesBundle\Data\CityLocator
     */
    protected $cityLocator;

    /**
     * @var \Weasty\Money\Currency\Converter\CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $categoryRepository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $proposalRepository;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $priceRepository;

    /**
     * @var \Shop\ShippingBundle\Calculator\ShippingCalculatorInterface
     */
    protected $shippingCalculator;

    /**
     * @param $shippingCalculator
     * @param $currencyConverter
     * @param $cityLocator
     * @param $categoryRepository
     * @param $proposalRepository
     * @param $priceRepository
     */
    function __construct(
        $shippingCalculator,
        $currencyConverter,
        $cityLocator,
        $categoryRepository,
        $proposalRepository,
        $priceRepository
    )
    {
        $this->shippingCalculator = $shippingCalculator;
        $this->currencyConverter = $currencyConverter;
        $this->cityLocator = $cityLocator;
        $this->categoryRepository = $categoryRepository;
        $this->proposalRepository = $proposalRepository;
        $this->priceRepository = $priceRepository;
    }

    /**
     * @param $storageData
     * @return ShopCart
     */
    public function createShopCart($storageData = array()){

        $shopCart = new ShopCart($this->shippingCalculator, $this->currencyConverter);
        $shopCart
            ->setCustomerCity($this->cityLocator->getCity())
        ;

        if(isset($storageData['categories']) && is_array($storageData['categories'])){

            $categories = $storageData['categories'];

            foreach($categories as $categoryId => $categoryData){

                if(isset($categoryData['proposalPrices']) && is_array($categoryData['proposalPrices'])){

                    $proposalPrices = array_filter($categoryData['proposalPrices']);
                    if(!$proposalPrices){
                        continue;
                    }

                    $categoryId = (int)$categoryId;
                    $shopCartCategory = $shopCart->getCategories()->get($categoryId);

                    if(!$shopCartCategory instanceof ShopCartCategory){

                        $category = $this->categoryRepository->findOneBy(array(
                            'id' => $categoryId,
                        ));

                        if($category instanceof CategoryInterface){

                            $shopCartCategory = new ShopCartCategory($category);
                            $shopCart->getCategories()->set($category->getId(), $shopCartCategory);

                        }

                    }

                    if(!$shopCartCategory instanceof ShopCartCategory){
                        continue;
                    }

                    foreach($proposalPrices as $proposalPriceData){

                        if(isset($proposalPriceData['id']) && isset($proposalPriceData['proposalId']) && isset($proposalPriceData['amount'])){

                            $proposalPriceAmount = floatval($proposalPriceData['amount']);

                            if($proposalPriceAmount > 0){

                                $priceId = (int)$proposalPriceData['id'];
                                $proposalId = (int)$proposalPriceData['proposalId'];

                                $shopCartProposal = $shopCartCategory->getProposals()->get($proposalId);

                                if(!$shopCartProposal instanceof ShopCartProposal){

                                    $proposal = $this->proposalRepository->findOneBy(array(
                                        'id' => $proposalId,
                                    ));

                                    if($proposal instanceof ProposalInterface){

                                        $shopCartProposal = new ShopCartProposal($proposal);
                                        $shopCartCategory->getProposals()->set($proposal->getId(), $shopCartProposal);

                                    }

                                }

                                if(!$shopCartProposal instanceof ShopCartProposal){
                                    continue;
                                }

                                $shopCartPrice = $shopCartProposal->getPrices()->get($priceId);

                                if(!$shopCartPrice instanceof ShopCartPrice) {

                                    $proposalPrice = $this->priceRepository->findOneBy(array(
                                        'id' => $priceId,
                                    ));

                                    if ($proposalPrice instanceof ProposalPriceInterface) {

                                        $shopCartPrice = new ShopCartPrice($proposalPrice);
                                        $shopCartProposal->getPrices()->set($proposalPrice->getId(), $shopCartPrice);

                                    }

                                }

                                if(!$shopCartPrice instanceof ShopCartPrice){
                                    continue;
                                }

                                $shopCartPrice
                                    ->setAmount($proposalPriceAmount)
                                    ->getItemPrice()
                                        ->setValue($this->currencyConverter->convert($shopCartPrice->getPrice()))
                                        ->setCurrency($this->currencyConverter->getCurrencyResource()->getDefaultCurrency())
                                ;

                            }

                        }

                    }

                }

            }

        }

        return $shopCart;

    }

} 