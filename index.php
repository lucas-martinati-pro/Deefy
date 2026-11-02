<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/src/vendor/autoload.php';

use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\exception\ConfigIOException;
use iutnc\deefy\repository\DeefyRepository;

session_start();

try {
    DeefyRepository::setConfig(__DIR__ . '/src/config/deefy.db.ini');
} catch (ConfigIOException $e) {

}

$dispatcher = new Dispatcher($_GET['action'] ?? 'default');
$dispatcher->run();
