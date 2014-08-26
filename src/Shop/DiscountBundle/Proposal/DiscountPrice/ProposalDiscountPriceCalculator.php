<?php
namespace Shop\DiscountBundle\Proposal\DiscountPrice;

use Shop\DiscountBundle\Data\DiscountPrice;
use Weasty\Money\Price\Price;
use Weasty\Money\Price\PriceInterface;

/**
 * Class ProposalDiscountPriceCalculator
 * @package Shop\DiscountBundle\Proposal\DiscountPrice
 */
class ProposalDiscountPriceCalculator {

    /**
     * @var \Weasty\Money\Currency\Converter\CurrencyConverterInterface
     */
    protected $currencyConverter;

    /**
     * @var \Shop\DiscountBundle\Proposal\DiscountPrice\ProposalDiscountPriceBuilder
     */
    protected $discountPriceBuilder;

    function __construct($currencyConverter, $discountPriceBuilder)
    {
        $this->currencyConverter = $currencyConverter;
        $this->discountPriceBuilder = $discountPriceBuilder;
    }

    /**
     * @param $proposalPrice
     * @param \Shop\DiscountBundle\Entity\ActionConditionInterface[] $discountConditions
     * @return null|DiscountPrice
     */
    public function calculate($proposalPrice, array $discountConditions = []){

        $discountConditionsAmount = count($discountConditions);

        if($discountConditionsAmount == 0){
            return null;
        }

        if(!$proposalPrice instanceof PriceInterface){
            $proposalPrice = new Price($proposalPrice, $this->currencyConverter->getCurrencyResource()->getDefaultCurrency());
        }

        /**
         * @var DiscountPrice|null $minDiscountPrice
         */
        $minDiscountPrice = null;

        foreach($discountConditions as $discountCondition){

            $discountPrice = $this->discountPriceBuilder->build($proposalPrice, $discountCondition);

            if(!$discountPrice instanceof DiscountPrice){
                continue;
            }

            if($minDiscountPrice && $minDiscountPrice->getValue() > $discountPrice->getValue()){

                $minDiscountPrice = $discountPrice;

            } else {

                $minDiscountPrice = $discountPrice;

            }

        }

        return $minDiscountPrice;

    }

} 