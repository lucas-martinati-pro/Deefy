<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use iutnc\deefy\dispatch\Dispatcher;

require_once 'vendor/autoload.php';

session_start();

iutnc\deefy\repository\DeefyRepository::setConfig('config/deefy.db.ini');

$dispacher = new Dispatcher($_GET['action'] ?? 'default');

$dispacher->run();