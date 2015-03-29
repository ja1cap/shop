<?php
namespace Shop\MainBundle\Data;

use Doctrine\Common\Persistence\ObjectRepository;
use Weasty\Bundle\CommonBundle\Entity\Settings;

/**
 * Class ShopSettingsResource
 * @package Shop\MainBundle\Data
 */
class ShopSettingsResource {

    /**
     * @var ObjectRepository
     */
    protected $settingsRepository;

    /**
     * @var Settings|null
     */
    protected $settings;

    function __construct(ObjectRepository $settingsRepository)
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * @return null|Settings
     */
    public function getSettings()
    {
        if($this->settings === null){

            $this->settings = $this->settingsRepository->findOneBy(array());

            if (!$this->settings) {

                $this->settings = new Settings();

            }

        }
        return $this->settings;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key){
        return $this->getSettings()->offsetGet($key);
    }

} 