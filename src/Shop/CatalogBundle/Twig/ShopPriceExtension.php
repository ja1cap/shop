<?php
namespace Shop\CatalogBundle\Twig;

use Weasty\Money\Twig\AbstractMoneyExtension;

/**
 * Class ShopPriceExtension
 * @package Shop\CatalogBundle\Twig
 */
class ShopPriceExtension extends AbstractMoneyExtension {

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('shop_price', array($this, 'formatShopPrice'), array('is_safe' => array('html'))),
        );
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
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'shop_price_extension';
    }

} 