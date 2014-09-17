<?php
namespace Shop\CatalogBundle\Twig;

use Weasty\Bundle\CatalogBundle\Proposal\ProposalInterface;
use Weasty\Money\Price\PriceInterface;
use Weasty\Money\Twig\AbstractMoneyExtension;

/**
 * Class PriceExtension
 * @package Shop\CatalogBundle\Twig
 */
class PriceExtension extends AbstractMoneyExtension
{

    /**
     * @var \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditionsBuilder
     */
    protected $proposalActionConditionsBuilder;

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('shop_price', [$this, 'formatShopPrice'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('shop_catalog_price', [$this, 'buildCatalogPrice']),
        ];
    }

    /**
     * @param $price
     * @param null $destinationCurrency
     * @return null|string
     */
    public function formatShopPrice($price, $destinationCurrency = null){
        return $this->formatPrice($price, null, $destinationCurrency);
    }

    /**
     * @param $proposalPriceData
     * @return array
     */
    public function buildCatalogPrice($proposalPriceData){

        $result = [
            'isMax' => false,
            'price' => null,
            'discountPrice' => null,
        ];

        $price = null;

        //Check if price is defined
        if(isset($proposalPriceData['price'])){
            $price = $proposalPriceData['price'];
        }

        //Return basic result if price is null
        if($price === null){
            return $result;
        }

        //Exchange price if it is instance of PriceInterface
        if($price instanceof PriceInterface){
            $exchangedPrice = $this->getCurrencyConverter()->convert($price);
        } else {
            $exchangedPrice = floatval((string)$price);
        }

        $result['price'] = $exchangedPrice;

        //Build discount price
        $discountPrice = null;
        $hasDiscount = (!isset($proposalPriceData['hasDiscount']) || (isset($proposalPriceData['hasDiscount']) && $proposalPriceData['hasDiscount']));
        if($hasDiscount && $this->getProposalActionConditionsBuilder()){

            $proposal = null;
            if(isset($proposalPriceData['proposal'])){
                $proposal = $proposalPriceData['proposal'];
            }

            $actionConditionIds = null;
            if(isset($proposalPriceData['actionConditionIds'])){
                $actionConditionIds = $proposalPriceData['actionConditionIds'];
            }

            if($proposal instanceof ProposalInterface && $actionConditionIds){
                $actionConditions = $this->getProposalActionConditionsBuilder()->build($proposal, $actionConditionIds);
                $discountPrice = $actionConditions->getDiscountPrice($exchangedPrice);
            }

            $result['discountPrice'] = $discountPrice;

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
        $result['isMax'] = $isMax;

        return $result;

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'shop_price_extension';
    }

    /**
     * @return \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditionsBuilder
     */
    public function getProposalActionConditionsBuilder()
    {
        return $this->proposalActionConditionsBuilder;
    }

    /**
     * @param \Shop\DiscountBundle\Proposal\ActionCondition\ProposalActionConditionsBuilder $proposalActionConditionsBuilder
     */
    public function setProposalActionConditionsBuilder($proposalActionConditionsBuilder)
    {
        $this->proposalActionConditionsBuilder = $proposalActionConditionsBuilder;
    }

} 