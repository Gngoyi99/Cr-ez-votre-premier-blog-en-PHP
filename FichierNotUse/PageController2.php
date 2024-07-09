<?php
namespace Blog\Twig\Controller;

use PDO;
use PDOException;
use Twig\Environment;

class PageController {
    private $twig;
    private $db;
    public $dbStatus;

    public function __construct(Environment $twig) {
        $this->twig = $twig;

        // Charger la configuration de la base de données
        $config = require __DIR__ . '/../../config/db.php';

        try {
            // Initialiser la connexion à la base de données
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            $this->db = new PDO($dsn, $config['user'], $config['password']);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->dbStatus = "Vous êtes connectés à la base de données.";
        } catch (PDOException $e) {
            $this->dbStatus = "Erreur de connexion à la base de données : " . $e->getMessage();
        }
    }

    public function index() {
        echo $this->twig->render('index.html.twig', ['dbStatus' => $this->dbStatus]);
    }

    public function home() {
        // Récupérer tous les utilisateurs
        $stmt = $this->db->query('SELECT * FROM User');
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Afficher la page d'accueil avec la liste des utilisateurs
        echo $this->twig->render('home.html.twig', ['dbStatus' => $this->dbStatus, 'users' => $users]);
    }

    public function userLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Vérification de l'utilisateur en base de données
            $stmt = $this->db->prepare('SELECT * FROM User WHERE username = :username');
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérification du mot de passe
            if ($user && $password === $user['password']) {
                // Démarrage de la session
                session_start();
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];

                // Redirection vers la page d'accueil après la connexion réussie
                header('Location: /BlogPHP/home');
                exit;
            } else {
                // Affichage du formulaire de connexion avec un message d'erreur
                echo $this->twig->render('User/userLogin.html.twig', [
                    'error' => 'Nom d\'utilisateur ou mot de passe incorrect'
                ]);
            }
        } else {
            // Affichage du formulaire de connexion
            echo $this->twig->render('User/userLogin.html.twig');
        }
    }

    public function userRegister() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $surname = $_POST['surname'];
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Hachage du mot de passe
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // Insertion de l'utilisateur dans la base de données
            $stmt = $this->db->prepare('INSERT INTO User (name, surname, username, password) VALUES (:name, :surname, :username, :password)');
            $stmt->execute([
                'name' => $name,
                'surname' => $surname,
                'username' => $username,
                'password' => $passwordHash
            ]);

            // Redirection vers la page de connexion après l'inscription réussie
            header('Location: /BlogPHP/userLogin');
            exit;
        } else {
            // Affichage du formulaire d'inscription
            echo $this->twig->render('User/userRegister.html.twig', ['dbStatus' => $this->dbStatus]);
        }
    }

    public function listPost() {
        echo $this->twig->render('Post/listPost.html.twig');
    }

    public function addPost() {
        echo $this->twig->render('Post/addPost.html.twig');
    }

    public function editDeleteComment() {
        echo $this->twig->render('Comment/editDeleteComment.html.twig');
    }

    public function logout() {
        // Détruire la session et rediriger vers la page d'accueil
        session_start();
        session_destroy();
        header('Location: /BlogPHP/home');
        exit;
    }

    public function notFound($uri) {
        echo $this->twig->render('Error/404.html.twig', ['uri' => $uri]);
    }
}

