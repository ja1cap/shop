<?php

namespace Shop\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ShopUserBundle
 * @package Shop\UserBundle
 */
class ShopUserBundle extends Bundle
{

    public function getParent()
    {
        return 'FOSUserBundle';
    }

}
