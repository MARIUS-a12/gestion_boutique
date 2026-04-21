<?php
require_once 'config.php';
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Récupérer les données JSON du panier
$data = json_decode(file_get_contents('php://input'), true);

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id']) || intval($_SESSION['user_id']) <= 0) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$user_id_value = $user_id > 0 ? $user_id : null;

if (!$data || !isset($data['articles'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Panier vide']);
    exit;
}

$articles = $data['articles'];
$total = isset($data['total']) ? floatval($data['total']) : 0;

// Vérifier que le panier n'est pas vide
if (empty($articles)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Le panier est vide']);
    exit;
}

try {
    $pdo = get_db_connection();

    // Démarrer une transaction
    $pdo->beginTransaction();

    // 1. Créer la commande
    $queryCommande = "INSERT INTO commande (users_id, date_commande, statut) 
                      VALUES (:user_id, NOW(), 'en attente')";
    
    $stmtCommande = $pdo->prepare($queryCommande);
    $stmtCommande->execute([':user_id' => $user_id]);
    
    $commande_id = $pdo->lastInsertId();

    // 2. Ajouter chaque article à la commande
    $queryArticle = "INSERT INTO ligne_commande (commande_id, publication_id, quantite, prix, publication_nom) 
                     VALUES (:commande_id, :publication_id, :quantite, :prix, :publication_nom)";
    
    $stmtArticle = $pdo->prepare($queryArticle);

    foreach ($articles as $article) {
        $publication_id = intval($article['publication_id']);
        $quantite = intval($article['quantite']);
        $prix = floatval($article['prix']);
        $publication_nom = $article['nom'];

        // Vérifier que l'article existe en base de données
        $queryCheck = "SELECT id, prix FROM publication WHERE id = :id";
        $stmtCheck = $pdo->prepare($queryCheck);
        $stmtCheck->execute([':id' => $publication_id]);
        $articleBDD = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if (!$articleBDD) {
            throw new Exception("Article $publication_id introuvable");
        }

        // Utiliser le prix de la BDD pour éviter les manipulations
        $prix_final = floatval($articleBDD['prix']);

        $stmtArticle->execute([
            ':commande_id' => $commande_id,
            ':publication_id' => $publication_id,
            ':quantite' => $quantite,
            ':prix' => $prix_final,
            ':publication_nom' => $publication_nom
        ]);
    }

    // Valider la transaction
    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Commande créée avec succès',
        'commande_id' => $commande_id
    ]);

} catch (PDOException $e) {
    // Annuler la transaction en cas d'erreur
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur base de données: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>