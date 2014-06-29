<?php
namespace Shop\UserBundle\Composer;

use Composer\Script\CommandEvent;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler as BaseScriptHandler;

/**
 * Class ScriptHandler
 * @package Shop\UserBundle\Composer
 */
class ScriptHandler extends BaseScriptHandler {

    /**
     * @param CommandEvent $event
     */
    public static function createUserGroup(CommandEvent $event){

        $options = self::getOptions($event);
        $appDir = $options['symfony-app-dir'];

        if (!is_dir($appDir)) {
            echo 'The symfony-app-dir ('.$appDir.') specified in composer.json was not found in '.getcwd().', can not clear the cache.'.PHP_EOL;

            return;
        }

        static::executeCommand($event, $appDir, 'user:createGroup', $options['process-timeout']);


    }

} 