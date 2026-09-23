<?php

use iutnc\deefy\dispatch\Dispatcher;
use iutnc\deefy\exception\ConfigIOException;
use iutnc\deefy\repository\DeefyRepository;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';

/**
// L'utilisateur reste connecté pendant 1 semaine
$duree = 60 * 60 * 24 * 7;
ini_set('session.gc_maxlifetime', $duree);
session_set_cookie_params([
    'lifetime' => $duree
]);
*/

session_start();

try {
    DeefyRepository::setConfig(__DIR__ . '/config/deefy.db.ini');
} catch (ConfigIOException $e) {

}

$dispatcher = new Dispatcher($_GET['action'] ?? 'default');
$dispatcher->run();
