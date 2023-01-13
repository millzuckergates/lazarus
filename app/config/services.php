<?php

use Phalcon\Cache;
use Phalcon\Cache\AdapterFactory;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Storage\SerializerFactory;
use Phalcon\Url as UrlResolver;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Manager as SessionManager;
use Phalcon\Session\Adapter\Stream as SessionAdapter;
use Phalcon\Flash\Direct as Flash;


use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Dispatcher\Exception as DispatcherException;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
      '.volt' => function ($view) {
          $config = $this->getConfig();

          $volt = new VoltEngine($view, $this);

          $volt->setOptions([
            'compiledPath' => $config->application->cacheDir,
            'compiledSeparator' => '_'
          ]);

          return $volt;
      },
      '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
      'host' => $config->database->host,
      'username' => $config->database->username,
      'password' => $config->database->password,
      'dbname' => $config->database->dbname,
      'charset' => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    return new $class($params);
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    $flash = new Flash();
    $flash->setCssClasses([
      'error' => 'alert alert-danger',
      'success' => 'alert alert-success',
      'notice' => 'alert alert-info',
      'warning' => 'alert alert-warning'
    ]);
    return $flash;
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionManager();
    $files = new SessionAdapter();
    $session->setAdapter($files);
    $session->start();

    return $session;
});

$di->setShared('dispatcher', function () {
    // Create an EventsManager
    $eventsManager = new EventsManager();

    // Attach a listener
    $eventsManager->attach('dispatch:beforeException',
      function (Event $event, $dispatcher, $exception) {
          switch ($exception->getCode()) {
              case DispatcherException::EXCEPTION_HANDLER_NOT_FOUND:
              case DispatcherException::EXCEPTION_ACTION_NOT_FOUND:
                  $dispatcher->forward([
                    'controller' => 'index',
                    'action' => 'error404',
                  ]);
                  return false;
          }
          return true;
      }
    );

    $dispatcher = new MvcDispatcher();

    // Bind the EventsManager to the dispatcher
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;
}
);

$di->set(
  'modelsCache',
  function () {
      $serializerFactory = new SerializerFactory();
      $adapterFactory    = new AdapterFactory($serializerFactory);

      $options = [
        'defaultSerializer' => 'Php',
        'lifetime'          => 300
      ];

      $adapter = $adapterFactory->newInstance('apcu', $options);

      return new Cache($adapter);
  }
);
	