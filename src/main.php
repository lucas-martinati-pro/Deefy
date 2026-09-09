<?php

use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\repository\DeefyRepository;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

session_start();

DeefyRepository::setConfig(__DIR__ . '/config/deefy.db.ini');

$dispacher = new Dispatcher($_GET['action'] ?? 'default');
$dispacher->run();
