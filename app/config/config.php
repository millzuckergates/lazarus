<?php

use Phalcon\Config;

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');
defined('SITE_NAME') || define('SITE_NAME', '/');

/** Décalage des années pour arriver à la date Ideo */
const DECALAGE_ANNEES = 457;

/** Retour chariot */
const NL = "\n";

return new Config([
  'database' => [
    'adapter' => 'Mysql',
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'dbname' => 'lazarus',
    'charset' => 'utf8',
  ],
  'application' => [
    'appDir' => APP_PATH . '/',
    'controllersDir' => APP_PATH . '/controllers/',
    'modelsDir' => APP_PATH . '/models/',
    'modelAssocDir' => APP_PATH . '/models/assoc/',
    'scriptNaturesMagieDir' => APP_PATH . '/models/scriptsnaturesmagie/',
    'scriptEcolesMagieDir' => APP_PATH . '/models/scriptsecolesmagie/',
    'scriptEffetsDir' => APP_PATH . '/models/scriptseffets/',
    'scriptContraintesDir' => APP_PATH . '/models/scriptscontraintes/',
    'scriptBonusDir' => APP_PATH . '/models/scriptsbonus/',
    'migrationsDir' => APP_PATH . '/migrations/',
    'viewsDir' => APP_PATH . '/views/',
    'pluginsDir' => APP_PATH . '/plugins/',
    'libraryDir' => APP_PATH . '/library/',
    'cacheDir' => APP_PATH . '/cache/',
    'logsDir' => APP_PATH . '/logs/',
    'utilsDir' => APP_PATH . '/utils/',
    'plateauDir' => APP_PATH . '/plateau/',
    'imgDir' => BASE_PATH . '/public/img/',
    'carteDir' => BASE_PATH . '/public/img/cartes/',
    'cartePJDir' => BASE_PATH . '/public/img/cartesPJ/',
    'gifDir' => BASE_PATH . '/public/gifs/',
    'terrainDir' => BASE_PATH . '/public/img/site/interface/terrains/',
    'magieScriptDir' => APP_PATH . '/magie/scripts/',

      // This allows the baseUri to be understand project paths that are not in the root directory
      // of the webpspace.  This will break if the public/index.php entry point is moved or
      // possibly if the web server rewrite rules are changed. This can also be set to a static path.
    'baseUri' => preg_replace('/(public([\/\\\\]))?index.php$/', '', $_SERVER["PHP_SELF"]),
  ]
]);
