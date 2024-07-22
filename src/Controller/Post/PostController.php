<?php
namespace Blog\Twig\Controller\Post;

use Blog\Twig\Controller\CoreController;
use Twig\Environment;
use Blog\Twig\Utils\Validator;
use PDO;

class PostController extends CoreController {

    public function listPost() {
        // Récupération des articles depuis la base de données
        $stmt = $this->db->query('SELECT * FROM Post ORDER BY updated_at DESC');
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Affichage de la liste des articles
        echo $this->twig->render('Post/listPost.html.twig', [
            'posts' => $posts
        ]);
    }

    public function addPost() {
        // Vérifiez si l'utilisateur est connecté
        if (!isset($_SESSION['id_user'])) {
            header('Location: /BlogPHP/userLogin');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $chapo = $_POST['chapo'];
            $content = $_POST['content'];
            $userId = $_SESSION['id_user'];

            // Validation des données grace à Validator
            $titleValid = Validator::validateMaxLength($title, 255);
            $chapoValid = Validator::validateMaxLength($chapo, 255);
            $contentValid = Validator::validateMaxLength($content, 255);

            if (!$titleValid || !$chapoValid || !$contentValid) {
                echo $this->twig->render('Post/addPost.html.twig', [
                    'errors' => [
                        'title' => $titleValid ? '' : 'Titre invalide',
                        'chapo' => $chapoValid ? '' : 'Chapô invalide',
                        'content' => $contentValid ? '' : 'Contenu invalide'
                    ]
                ]);
                return;
            }

            // Insertion du post dans la base de données
            $stmt = $this->db->prepare('INSERT INTO Post (title, chapo, content, user_id) VALUES (:title, :chapo, :content, :user_id)');
            $stmt->execute([
                'title' => htmlspecialchars($title),
                'chapo' => htmlspecialchars($chapo),
                'content' => htmlspecialchars($content),
                'user_id' => $userId
            ]);

            // Redirection vers la liste des posts quan le post est bien enregistré
            header('Location: /BlogPHP/listPost');
            exit;
        } else {
            // Affichage du formulaire d'ajout de post
            echo $this->twig->render('Post/addPost.html.twig');
        }
    }

    public function editPost($postId) {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
            header('Location: /BlogPHP/userLogin');
            exit();
        }
    
        // Récupérer les informations de l'article
        $stmt = $this->db->prepare('SELECT * FROM Post WHERE postId = :postId');
        $stmt->execute(['postId' => $postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$post) {
            echo "Article introuvable.";
            return;
        }
    
        // Valider les droits de l'utilisateur
        if ($_SESSION['id_user'] !== $post['user_id']) {
            echo "Vous n'avez pas les droits pour modifier cet article.";
            return;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'];
            $chapo = $_POST['chapo'];
            $content = $_POST['content'];
    
            // // Valider les données
            // if (!Validator::validateMaxLength($title, 255)) {
            //     echo "Le titre ne peut pas être vide ou dépasser 255 caractères.";
            //     return;
            // }
    
            // if (!Validator::validateMaxLength($chapo, 255)) {
            //     echo "Le chapô ne peut pas être vide ou dépasser 255 caractères.";
            //     return;
            // }
    
            // if (!Validator::validateContent($content)) {
            //     echo "Le contenu ne peut pas être vide.";
            //     return;
            // }
    
            // Mettre à jour l'article dans la base de données
            $stmt = $this->db->prepare('UPDATE Post SET title = :title, chapo = :chapo, content = :content WHERE postId = :postId');
            $stmt->execute([
                'title' => $title,
                'chapo' => $chapo,
                'content' => $content,
                'postId' => $postId
            ]);
    
            // Rediriger vers le post mis à jour
            header("Location: /BlogPHP/showPost/{$postId}");
            return;
        }
    
        // Afficher le formulaire d'édition
        echo $this->twig->render('Post/editPost.html.twig', ['post' => $post]);
    }
    
    
    public function deletePost($postId) {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
            header('Location: /BlogPHP/userLogin');
            exit();
        }
    
        // Récupérer les informations de l'article
        $stmt = $this->db->prepare('SELECT * FROM Post WHERE postId = :postId');
        $stmt->execute(['postId' => $postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$post) {
            echo "Article introuvable.";
            return;
        }
    
        // Valider les droits de l'utilisateur
        if ($_SESSION['id_user'] !== $post['user_id']) {
            echo "Vous n'avez pas les droits pour supprimer cet article.";
            return;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Supprimer l'article de la base de données
            $stmt = $this->db->prepare('DELETE FROM Post WHERE postId = :postId');
            $stmt->execute(['postId' => $postId]);
    
            // Rediriger vers la liste des articles
            header('Location: /BlogPHP/listPost');
            return;
        }
    
        // Afficher la confirmation de suppression
        echo $this->twig->render('Post/deletePost.html.twig', ['post' => $post]);
    }
    

    public function showPost($postId) {
        // Récupérer les détails du post
        $stmt = $this->db->prepare('SELECT * FROM Post WHERE postId = :postId');
        $stmt->execute(['postId' => $postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        // Récupérer les commentaires associés
        $commentsStmt = $this->db->prepare('SELECT * FROM CommentPost WHERE postId = :postId ORDER BY created_at DESC');
        $commentsStmt->execute(['postId' => $postId]);
        $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

        // Rendre le template avec les données du post et les commentaires
        echo $this->twig->render('Post/showPost.html.twig', [
            'post' => $post,
            'comments' => $comments
        ]);
    }
    

    
}
