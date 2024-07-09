<?php
namespace Blog\Twig\Controller\Comment;

use PDO;
use Twig\Environment;

class CommentController {
    private $twig;

    public function __construct(Environment $twig) {
        $this->twig = $twig;
    }

    public function editDeleteComment() {
        echo $this->twig->render('Comment/editDeleteComment.html.twig');
    }
}
