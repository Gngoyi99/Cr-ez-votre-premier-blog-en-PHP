<?php

require_once 'vendor/autoload.php';

use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Configuration de Twig
$loader = new FilesystemLoader(__DIR__ . '/src/Template/');
$twig = new Environment($loader);
var_dump($loader);

// Inclure et appeler le routeur
require 'router.php';

