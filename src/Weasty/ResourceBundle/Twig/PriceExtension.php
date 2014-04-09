<?php
namespace Weasty\ResourceBundle\Twig;
use Weasty\ResourceBundle\Data\PriceInterface;

/**
 * Class PriceExtension
 * @package Weasty\ResourceBundle\Twig
 */
class PriceExtension extends \Twig_Extension {

    public function formatPrice($price){

        if($price instanceof PriceInterface){



        }

    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'weasty_price_extension';
    }

} 