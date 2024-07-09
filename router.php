<?php
require_once 'vendor/autoload.php';

use Blog\Twig\Controller\Main\MainController;
use Blog\Twig\Controller\User\UserController;
use Blog\Twig\Controller\Post\PostController;
use Blog\Twig\Controller\Comment\CommentController;
use Blog\Twig\Controller\Error\ErrorController;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Définir le chemin racine du projet(Problème lors de la création class Abs avec le fichier config.php)
define('BASE_PATH', __DIR__);


// Démarrer la session
session_start();

// Configuration de Twig
$loader = new FilesystemLoader(BASE_PATH . '/src/Template/');
$twig = new Environment($loader);

// Initialisation des contrôleurs
$mainController = new MainController($twig);
$userController = new UserController($twig);
$postController = new PostController($twig);
$commentController = new CommentController($twig);
$errorController = new ErrorController($twig);

// Fonction pour gérer les routes
function handleRoute($mainController, $userController, $postController, $commentController, $errorController) {
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // Si l'URI commence par /BlogPHP, on enlève cette partie le routage
    if (strpos($uri, '/BlogPHP') === 0) {
        $uri = substr($uri, strlen('/BlogPHP'));
    }

    switch ($uri) {
        case '/':
        case '':
        case '/index.php':
            // Rediriger vers la page d'accueil
            header('Location: /BlogPHP/home');
            break;
        case '/home':
            $mainController->home();
            break;
        case '/userLogin':
            $userController->userLogin();
            break;
        case '/userRegister':
            $userController->userRegister();
            break;
        case '/listPost':
            $postController->listPost();
            break;
        case '/showPost':
            $postController->showPost();
            break;
        case '/addPost':
            $postController->addPost();
            break;
        case '/editDeletePost':
            $postController->editDeletePost();
            break;
        case '/editDeleteComment':
            $commentController->editDeleteComment();
            break;
        case '/logout':
            $userController->logout();
            break;
        default:
            $errorController->notFound($uri);
            break;
    }
}

// Passer le nom de l'utilisateur à Twig
$twig->addGlobal('username', $_SESSION['username'] ?? null);

// Appel de la fonction de routage
handleRoute($mainController, $userController, $postController, $commentController, $errorController);