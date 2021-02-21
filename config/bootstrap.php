<?php
date_default_timezone_set('America/Los_Angeles');

use RedBeanPHP\R;

// Setting up database connection
R::setup(
$config['db']['db_conn_str'],
$config['db']['username'],
$config['db']['password']
);

spl_autoload_register(function ($class) {
if(substr($class,0,8) == 'TLegasse') include '../src/' . str_replace('\\',"/",$class) . '.php';
});

session_start();

include 'routes.php';