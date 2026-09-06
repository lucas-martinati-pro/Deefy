<?php

use iutnc\deefy\dispatch\Dispatcher;

require_once 'vendor/autoload.php';

session_start();

$dispacher = new Dispatcher($_GET['action'] ?? 'default');

$dispacher->run();