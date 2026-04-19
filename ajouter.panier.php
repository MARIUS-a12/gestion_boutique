<?php
session_start();
header('Content-Type: application/json');

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non authentifié']);
    exit;
}

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['action']) || !isset($data['publication_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $data['action'];
$publication_id = intval($data['publication_id']);
$quantite = isset($data['quantite']) ? intval($data['quantite']) : 0;

try {
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "boutique";
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insérer le log dans panier_logs
    $query = "INSERT INTO panier_logs (user_id, publication_id, action, quantite) 
              VALUES (:user_id, :publication_id, :action, :quantite)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':user_id' => $user_id,
        ':publication_id' => $publication_id,
        ':action' => $action,
        ':quantite' => $quantite
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Action tracée avec succès',
        'action' => $action
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur base de données: ' . $e->getMessage()
    ]);
}
?>