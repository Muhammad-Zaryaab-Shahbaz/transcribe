<?php
session_start();
include_once('./config/config.php');
include_once('./controllers/functions.php');
include_once('./controllers/db-functions.php');
include_once('./constants.php');
include_once('./route.php');

$routes = new Routes();
$routes->configureRoutes();
