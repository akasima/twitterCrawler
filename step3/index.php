<?php

$config = include_once('configs/app.php');
include_once('includes/helpers.php');

// vendor 의 autoload
include_once('vendor/autoload.php');

// load controller
include_once('controllers/' . $config['controllerName'] . '.php');

// load view
include_once('resources/views/' . $config['controllerName'] . '.php');
