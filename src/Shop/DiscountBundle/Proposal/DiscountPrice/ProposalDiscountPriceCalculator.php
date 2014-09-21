<?php
namespace Shop\DiscountBundle\Proposal\DiscountPrice;

use Shop\DiscountBundle\ActionCondition\ActionConditionInterface;
use Shop\DiscountBundle\Price\DiscountPrice;
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
     * @param \Shop\DiscountBundle\ActionCondition\ActionConditionInterface $discountCondition
     * @return null|DiscountPrice
     */
    public function calculate($proposalPrice, ActionConditionInterface $discountCondition = null){

        if(!$discountCondition){
            return null;
        }

        if(!$proposalPrice instanceof PriceInterface){
            $proposalPrice = new Price($proposalPrice, $this->currencyConverter->getCurrencyResource()->getDefaultCurrency());
        }

        $discountPrice = $this->discountPriceBuilder->build($proposalPrice, $discountCondition);

        return $discountPrice;

    }

} 