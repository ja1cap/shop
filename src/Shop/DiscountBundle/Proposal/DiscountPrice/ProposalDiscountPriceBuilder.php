<?php
namespace Shop\DiscountBundle\Proposal\DiscountPrice;
use Shop\DiscountBundle\Price\DiscountPrice;
use Shop\DiscountBundle\Entity\ActionConditionInterface;
use Weasty\Money\Price\PriceInterface;

/**
 * Class ProposalDiscountPriceBuilder
 * @package Shop\DiscountBundle\Proposal\DiscountPrice
 */
class ProposalDiscountPriceBuilder {

    /**
     * @var \Weasty\Money\Currency\Converter\CurrencyConverterInterface
     */
    protected $currencyConverter;

    function __construct($currencyConverter)
    {
        $this->currencyConverter = $currencyConverter;
    }

    /**
     * @param $proposalPrice
     * @param $discountCondition
     * @return null|\Shop\DiscountBundle\Price\DiscountPrice
     */
    public function build($proposalPrice, $discountCondition){

        if(
            !$proposalPrice instanceof PriceInterface
            || !$discountCondition instanceof ActionConditionInterface
        ){
            return null;
        }

        if(!$discountCondition->getIsPriceDiscount()){
            return null;
        }

        switch($discountCondition->getDiscountType()){
            case ActionConditionInterface::DISCOUNT_TYPE_PRICE:
            case ActionConditionInterface::DISCOUNT_TYPE_GIFT_AND_PRICE:
            case ActionConditionInterface::DISCOUNT_TYPE_GIFT_OR_PRICE:

                $discountPriceValue = $this->currencyConverter->convert(
                    $discountCondition->getDiscountPriceValue(),
                    $discountCondition->getDiscountPriceCurrencyNumericCode(),
                    $proposalPrice->getCurrency()
                );

                if($discountPriceValue >= $proposalPrice->getValue()){

                    $discountPrice = null;

                } else {

                    $discountPrice = new DiscountPrice($discountPriceValue, $proposalPrice->getCurrency());
                    $discountPrice
                        ->setOriginalPrice($proposalPrice)
                        ->setDiscountCondition($discountCondition)
                    ;

                }

                break;
            case ActionConditionInterface::DISCOUNT_TYPE_PERCENT:
            case ActionConditionInterface::DISCOUNT_TYPE_GIFT_AND_PERCENT:
            case ActionConditionInterface::DISCOUNT_TYPE_GIFT_OR_PERCENT:

                $discountPriceValue = ($proposalPrice->getValue() - (($proposalPrice->getValue() / 100) * $discountCondition->getDiscountPercent()));

                $discountPrice = new DiscountPrice($discountPriceValue, $proposalPrice->getCurrency());
                $discountPrice
                    ->setOriginalPrice($proposalPrice)
                    ->setDiscountPercent($discountCondition->getDiscountPercent())
                    ->setDiscountCondition($discountCondition)
                ;

                break;
            default:
                //@TODO throw exception invalid discount type
                return null;
        }

        return $discountPrice;

    }

} 