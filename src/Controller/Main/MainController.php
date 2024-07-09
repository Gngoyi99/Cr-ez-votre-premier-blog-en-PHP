<?php
namespace Blog\Twig\Controller\Main;

use Blog\Twig\Controller\DbController;
use PDO;

class MainController extends DbController {

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
        // Vérifiez si l'utilisateur est connecté
        if (!isset($_SESSION['username'])) {
            // Redirigez vers la page d'accueil générale
            echo $this->twig->render('index.html.twig');
            return;
        }

        // Récupérer tous les utilisateurs
        $stmt = $this->db->query('SELECT * FROM User');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Afficher la page d'accueil avec la liste des utilisateurs
        echo $this->twig->render('home.html.twig', [
            'dbStatus' => $this->dbStatus, 
            'users' => $users, 
            'username' => $_SESSION['username']
        ]);
    }
}
