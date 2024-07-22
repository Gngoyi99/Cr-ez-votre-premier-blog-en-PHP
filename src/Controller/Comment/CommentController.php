<?php
namespace Blog\Twig\Controller\Comment;

use Blog\Twig\Controller\CoreController;
use Blog\Twig\Utils\Validator;

use PDO;

class CommentController extends CoreController {

    public function addComment($postId) {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
            header('Location: /BlogPHP/userLogin');
            exit();
        }

        // Récupérer les données du formulaire
        $content = $_POST['content'];
        $userId = $_SESSION['id_user'];

        // Valider les données

        if (!Validator::validateMaxLength($content, 255)) {
            echo "Le contenu du commentaire ne peut pas dépasser 255 caractères.";
            return;
        }

        // Insérer le commentaire dans la base de données
        $stmt = $this->db->prepare('INSERT INTO CommentPost (content, id_user, postId, created_at) VALUES (:content, :id_user, :postId, NOW())');
        $stmt->execute([
            'content' => $content,
            'id_user' => $userId,
            'postId' => $postId
        ]);

        // Rediriger vers le post
        header("Location: /BlogPHP/showPost/$postId");
    }

    public function editComment($commentId) {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
            header('Location: /BlogPHP/userLogin');
            exit();
        }

        // Récupérer les informations du commentaire
        $stmt = $this->db->prepare('SELECT * FROM CommentPost WHERE commentId = :commentId');
        $stmt->execute(['commentId' => $commentId]);
        $comment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$comment) {
            echo "Commentaire introuvable.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['content'];

            // Valider les données
            

            if (!Validator::validateMaxLength($content, 255)) {
                echo "Le contenu du commentaire ne peut pas dépasser 255 caractères.";
                return;
            }

            // Mettre à jour le commentaire dans la base de données
            $stmt = $this->db->prepare('UPDATE CommentPost SET content = :content WHERE commentId = :commentId');
            $stmt->execute([
                'content' => $content,
                'commentId' => $commentId
            ]);

            // Rediriger vers le post
            header("Location: /BlogPHP/showPost/{$comment['postId']}");
            return;
        }

        // Afficher le formulaire d'édition
        echo $this->twig->render('Comment/editComment.html.twig', ['comment' => $comment]);
    }

    public function deleteComment($commentId) {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['username']) || !isset($_SESSION['id_user'])) {
            header('Location: /BlogPHP/userLogin');
            exit();
        }

        // Supprimer le commentaire de la base de données
        $stmt = $this->db->prepare('DELETE FROM CommentPost WHERE commentId = :commentId');
        $stmt->execute(['commentId' => $commentId]);

        // Rediriger vers la page précédente
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }

}
