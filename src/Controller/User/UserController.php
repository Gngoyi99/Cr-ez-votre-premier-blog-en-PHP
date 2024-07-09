<?php
namespace Blog\Twig\Controller\User;

use Blog\Twig\Controller\DbController;
use PDO;

class UserController extends DbController {
    
    public function userLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Vérification de l'utilisateur en base de données
            $stmt = $this->db->prepare('SELECT * FROM User WHERE username = :username');
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Vérification du mot de passe
            if ($user && password_verify($password, $user['password'])) {
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
            // Récupération des données du formulaire
            $name = $_POST['name'];
            $surname = $_POST['surname'];
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Validation des données côté serveur
            $nameValid = $this->validateName($name);
            $surnameValid = $this->validateSurname($surname);
            $usernameValid = $this->validateUsername($username);
            $emailValid = filter_var($email, FILTER_VALIDATE_EMAIL);
            $passwordValid = $this->validatePassword($password);


            if (!$nameValid || !$surnameValid || !$usernameValid || !$emailValid || !$passwordValid) {
                // Affichage du formulaire d'inscription avec les erreurs
                echo $this->twig->render('User/userRegister.html.twig', [
                    'dbStatus' => $this->dbStatus,
                    'error' => 'Veuillez corriger les erreurs dans le formulaire'
                ]);
                return;
            }

            // Hachage du mot de passe
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);

            // Insertion de l'utilisateur dans la base de données
            $stmt = $this->db->prepare('INSERT INTO User (name, surname, username, email, password) VALUES (:name, :surname, :username, :email, :password)');
            $stmt->execute([
                'name' => $name,
                'surname' => $surname,
                'username' => $username,
                'email' => $email,
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

    private function validateName($name) {
        // Autoriser les lettres, espaces, apostrophes et tirets
        $nameRegex = '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s\'\-]+$/';
        return preg_match($nameRegex, $name);
    }

    private function validateSurname($surname) {
        // Autoriser les lettres, espaces, apostrophes et tirets
        $surnameRegex = '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s\'\-]+$/';
        return preg_match($surnameRegex, $surname);
    }

    private function validateUsername($username) {
        // Autoriser les lettres, chiffres et soulignements
        $usernameRegex = '/^[a-zA-Z0-9_]+$/';
        return preg_match($usernameRegex, $username);
    }

    private function validatePassword($password) {
        // Vérifier si le mot de passe contient au moins 8 caractères avec une majuscule, des chiffres et un caractère spécial
        $passwordRegex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d\s]).{8,}$/';
        return preg_match($passwordRegex, $password);
    }


    public function logout() {
        // Détruire la session et rediriger vers la page d'accueil
        session_start();
        session_destroy();
        header('Location: /BlogPHP/home');
        exit;
    }
}
