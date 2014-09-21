<?php
namespace Shop\CatalogBundle\Price\Catalog;

use Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface;
use Shop\CatalogBundle\Price\ProposalPriceInterface;
use Weasty\Money\Price\PriceInterface;

/**
 * Class CatalogPriceBuilder
 * @package Shop\CatalogBundle\Price\Catalog
 */
class CatalogPriceBuilder {

    /**
     * @var \Weasty\Money\Currency\Converter\CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @var \Weasty\Doctrine\Cache\Collection\CacheCollectionManager
     */
    protected $cacheCollectionManager;

    /**
     * @var \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditionsBuilder
     */
    protected $proposalActionConditionsBuilder;

    function __construct($currencyConverter, $proposalActionConditionsBuilder, $cacheCollectionManager)
    {
        $this->currencyConverter = $currencyConverter;
        $this->proposalActionConditionsBuilder = $proposalActionConditionsBuilder;
        $this->cacheCollectionManager = $cacheCollectionManager;
    }


    /**
     * @param $proposalPriceData
     * @return array
     */
    public function build($proposalPriceData){

        $catalogPrice = [
            'isMax' => false,
            'price' => null,
            'hasAction' => false,
            'actionCondition' => null,
            'hasDiscount' => false,
            'discountPrice' => null,
        ];

        $price = null;
        $priceId = null;

        //Check if price and priceId are defined
        if(isset($proposalPriceData['price']) && isset($proposalPriceData['priceId'])){

            $price = $proposalPriceData['price'];
            $priceId = $proposalPriceData['priceId'];

        } else {

            //@TODO throw exception

        }

        //Return basic result if price is null
        if($price === null){
            return $catalogPrice;
        }

        //Exchange price if it is instance of PriceInterface
        if($price instanceof PriceInterface){
            $exchangedPrice = $this->currencyConverter->convert($price);
        } else {
            $exchangedPrice = floatval((string)$price);
        }

        $catalogPrice['price'] = $exchangedPrice;

        $hasAction = (!isset($proposalPriceData['hasAction']) || (isset($proposalPriceData['hasAction']) && $proposalPriceData['hasAction']));

        $actionConditionIds = null;
        if(isset($proposalPriceData['actionConditionIds'])){
            $actionConditionIds = $proposalPriceData['actionConditionIds'];
        }

        $actionConditions = null;
        if($hasAction && $actionConditionIds){

            $proposal = null;
            if(isset($proposalPriceData['proposal'])){
                $proposal = $proposalPriceData['proposal'];
            }

            if($price instanceof ProposalPriceInterface){
                $proposalPrice = $price;
            } else {
                $proposalPriceCollection = $this->cacheCollectionManager->getCollection('ShopCatalogBundle:Price');
                $proposalPrice = $proposalPriceCollection->get($priceId);
            }

            if($proposal instanceof ProposalInterface && $proposalPrice instanceof ProposalPriceInterface){
                $actionConditions = $this->proposalActionConditionsBuilder->build($proposal, $proposalPrice, $actionConditionIds);
            }

        }

        if($actionConditions){

            $catalogPrice['hasAction'] = true;
            $catalogPrice['actionCondition'] = $actionConditions->getMainCondition();

        }

        //Build discount price
        $discountPrice = null;
        $hasDiscount = (!isset($proposalPriceData['hasDiscount']) || (isset($proposalPriceData['hasDiscount']) && $proposalPriceData['hasDiscount']));
        if($hasDiscount && $actionConditions){

            $discountPrice = $actionConditions->getDiscountPrice($exchangedPrice);

            if($discountPrice){

                $catalogPrice['hasDiscount'] = true;
                $catalogPrice['discountPrice'] = $discountPrice;

            }

        }

        //Check if max price
        $isMax = false;
        if(isset($proposalPriceData['maxPrice'])){

            $maxPrice = $proposalPriceData['maxPrice'];
            if($maxPrice !== null){

                if($discountPrice){

                    $isMax = ($discountPrice->getValue() >= $maxPrice);

                } else {

                    $isMax = ($exchangedPrice >= $maxPrice);

                }

            }

        }
        $catalogPrice['isMax'] = $isMax;

        return $catalogPrice;

    }


} 