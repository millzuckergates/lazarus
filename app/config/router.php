<?php

$router = $di->get("router");

$router->handle($_SERVER["REQUEST_URI"]);
