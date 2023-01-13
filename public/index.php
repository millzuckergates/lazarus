<?php

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Application;

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
const APP_PATH = BASE_PATH . '/app';
const SITE_NAME = '/';
const VERSION = '0.0.3';
try {

    /**
     * The FactoryDefault Dependency Injector automatically registers
     * the services that provide a full stack framework.
     */
    $di = new FactoryDefault();

    /**
     * Handle routes
     */
    include APP_PATH . '/config/router.php';

    /**
     * Read services
     */
    include APP_PATH . '/config/services.php';

    /**
     * Get config service for use in inline setup below
     */
    $config = $di->get("config");

    /**
     * Include Autoloader
     */
    include APP_PATH . '/config/loader.php';

    /**
     * Include composer Autoloader
     */
    include BASE_PATH . '/vendor/autoload.php';

    /**
     * Handle the request
     */
    $application = new Application($di);

    echo str_replace(["\n", "\r", "\t"], '', $application->handle($_SERVER["REQUEST_URI"])->getContent());

} catch (Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
