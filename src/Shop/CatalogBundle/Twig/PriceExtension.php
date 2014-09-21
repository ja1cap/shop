<?php
namespace Shop\CatalogBundle\Twig;

use Weasty\Money\Twig\AbstractMoneyExtension;

/**
 * Class PriceExtension
 * @package Shop\CatalogBundle\Twig
 */
class PriceExtension extends AbstractMoneyExtension
{

    /**
     * @var \Shop\CatalogBundle\Price\Catalog\CatalogPriceBuilder
     */
    protected $catalogPriceBuilder;

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
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'shop_price_extension';
    }

    /**
     * @param $proposalPriceData
     * @return array
     */
    public function buildCatalogPrice($proposalPriceData){
        return $this->catalogPriceBuilder->build($proposalPriceData);
    }

    /**
     * @param \Shop\CatalogBundle\Price\Catalog\CatalogPriceBuilder $catalogPriceBuilder
     */
    public function setCatalogPriceBuilder($catalogPriceBuilder)
    {
        $this->catalogPriceBuilder = $catalogPriceBuilder;
    }

} 