<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'boutique'); // Note: dans le code, c'est 'boutique', pas 'gestion_boutique'
define('DB_USER', 'root');
define('DB_PASS', '');

// Mode développement (true pour afficher les erreurs, false pour production)
define('DEV_MODE', true);

// Clé secrète pour les tokens CSRF (changez-la en production)
define('CSRF_SECRET', 'votre_cle_secrete_unique_ici');

// Fonction pour générer un token CSRF
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fonction pour vérifier un token CSRF
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Fonction pour se connecter à la base de données
function get_db_connection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (DEV_MODE) {
                die('Erreur de connexion : ' . $e->getMessage());
            } else {
                die('Erreur de base de données.');
            }
        }
    }
    return $pdo;
}

// Démarrer la session si nécessaire
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
