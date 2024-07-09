<?php
namespace Blog\Twig\Controller\Error;

use PDO;
use Twig\Environment;

class ErrorController {
    private $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    public function notFound($uri) {
        echo $this->twig->render('Error/404.html.twig', ['uri' => $uri]);
    }
}
