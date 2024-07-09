<?php
namespace Blog\Twig\Controller\Post;

use Blog\Twig\Controller\DbController;
use Twig\Environment;

class PostController extends DbController {

    public function listPost() {
        echo $this->twig->render('Post/listPost.html.twig');
    }

    public function addPost() {
        echo $this->twig->render('Post/addPost.html.twig');
    }

    public function editDeletePost() {
        echo $this->twig->render('Post/editDeletePost.html.twig');
    }

    public function showPost() {
        echo $this->twig->render('Post/showPost.html.twig');
    }
}
