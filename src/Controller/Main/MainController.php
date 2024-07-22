<?php
namespace Blog\Twig\Controller\Main;

use Blog\Twig\Controller\CoreController;
use PDO;

class MainController extends CoreController {

    public function index() {
        // Vérifiez si l'utilisateur est connecté
        if (isset($_SESSION['username'])) {
            // Redirigez vers la page d'accueil personnalisée
            $this->home();
        } else {
            // Redirigez vers la page d'accueil générale
            echo $this->twig->render('index.html.twig');
        }
    }

    public function home() {
        // Vérifie si l'utilisateur est connecté et si les informations de l'utilisateur sont disponibles dans la session
        if (isset($_SESSION['id_user'])) {
            $userId = $_SESSION['id_user'];

            // Récupération des informations de l'utilisateur depuis la base de données
            $stmt = $this->db->prepare('SELECT * FROM User WHERE id_user = :id_user');
            $stmt->execute(['id_user' => $userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Affichage de la page d'accueil avec les informations de l'utilisateur
            echo $this->twig->render('home.html.twig', [
                'user' => $user
            ]);
        } else {
            // Si l'utilisateur n'est pas connecté ou s'il n'y a pas d'informations d'utilisateur en session, afficher la page d'accueil sans données d'utilisateur
            echo $this->twig->render('index.html.twig');
        }
    }
}
