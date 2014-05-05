<?php
namespace Shop\MainBundle\Twig;


/**
 * Class ShopSettingsExtension
 * @package Shop\MainBundle\Twig
 */
class ShopSettingsExtension extends \Twig_Extension {

    /**
     * @var \Shop\MainBundle\Data\ShopSettingsResource
     */
    protected $resource;

    function __construct($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'settings' => $this->resource->getSettings(), //@TODO use shop_settings variable
            'shop_settings' => $this->resource->getSettings(),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'shop_main_settings';
    }

} 