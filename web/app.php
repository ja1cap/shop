<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance.
// Change 'sf2' to a unique prefix in order to prevent cache key conflicts
// with other applications also using APC.

if(extension_loaded('apc')){

    $namespacePrefix = pathinfo($_SERVER['SERVER_NAME'], PATHINFO_FILENAME);
    $apcLoader = new ApcClassLoader($namespacePrefix, $loader);
    $loader->unregister();
    $apcLoader->register(true);

}

//require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';

$topLevelDomain = pathinfo($_SERVER['SERVER_NAME'], PATHINFO_EXTENSION);
$env = ($topLevelDomain == 'local' ? 'dev' : 'prod');
//$env = 'prod';
$kernel = new AppKernel($env, ($env == 'dev'));
$kernel->loadClassCache();
$kernel = new AppCache($kernel);

// When using the HttpCache, you need to call the method in your front controller instead of relying on the configuration parameter
Request::enableHttpMethodParameterOverride();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);