<?php

use iutnc\deefy\dispatch\Dispatcher;

require_once 'vendor/autoload.php';

session_start();

iutnc\deefy\repository\DeefyRepository::setConfig('config/deefy.db.ini');

$dispacher = new Dispatcher($_GET['action'] ?? 'default');

$dispacher->run();