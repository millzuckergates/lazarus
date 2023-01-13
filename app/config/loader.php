<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerDirs(
  [
    $config->application->controllersDir,
    $config->application->modelsDir,
    $config->application->modelAssocDir,
    $config->application->scriptNaturesMagieDir,
    $config->application->scriptEcolesMagieDir,
    $config->application->scriptEffetsDir,
    $config->application->scriptContraintesDir,
    $config->application->utilsDir,
    $config->application->plateauDir,
    $config->application->scriptBonusDir
  ]
)->register();
