<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['action']) || !isset($data['publication_id'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$action = $data['action'];
$publication_id = intval($data['publication_id']);
$quantite = isset($data['quantite']) ? intval($data['quantite']) : 0;
$publication_nom = isset($data['publication_nom']) ? trim($data['publication_nom']) : '';

try {
    $pdo = get_db_connection();

    // Insérer le log dans panier_logs
    $query = "INSERT INTO panier_logs (user_id, publication_id, action, quantite, publication_nom) 
              VALUES (:user_id, :publication_id, :action, :quantite, :publication_nom)";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':user_id' => $user_id,
        ':publication_id' => $publication_id,
        ':action' => $action,
        ':quantite' => $quantite,
        ':publication_nom' => $publication_nom
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