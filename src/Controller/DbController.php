<?php
namespace Blog\Twig\Controller;

use PDO;
use PDOException;
use Twig\Environment;

abstract class DbController {
    protected $twig;
    protected $db;
    public $dbStatus;

    public function __construct(Environment $twig) {
        $this->twig = $twig;

        // Charger la configuration de la base de données
        $configPath = BASE_PATH . '/config/db.php';

            // Résolution du problème de chemin
            if (!file_exists($configPath)) {
                throw new \Exception("Configuration file not found: $configPath");
            }

        $config = require $configPath;
        
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
}
