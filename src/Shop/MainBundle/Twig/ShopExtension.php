<?php
namespace Shop\MainBundle\Twig;


/**
 * Class ShopExtension
 * @package Shop\MainBundle\Twig
 */
class ShopExtension extends \Twig_Extension {

    /**
     * @var \Shop\MainBundle\Data\ShopSettingsResource
     */
    protected $settingsResource;

    /**
     * @var \Shop\MainBundle\Data\ShopContactsResource
     */
    protected $contactsResource;

    function __construct($settingsResource, $contactsResource)
    {
        $this->settingsResource = $settingsResource;
        $this->contactsResource = $contactsResource;
    }

    /**
     * @return array
     */
    public function getGlobals()
    {
        return array(
            'settings' => $this->settingsResource->getSettings(), //@TODO use shop_settings variable
            'shop_settings' => $this->settingsResource->getSettings(),
            'shop_contacts' => $this->contactsResource,
            'word_numbers' => ['zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve'],
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'shop';
    }

} 