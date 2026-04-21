<?php
require_once 'config.php';

$db = get_db_connection();

// Requête qui calcule la quantité disponible (quantité en BDD - quantité commandée)
$sql = "SELECT 
            p.id,
            p.categorie,
            p.description,
            p.prix,
            p.image,
            p.quantité,
            COALESCE(SUM(lc.quantite), 0) as quantite_commandee,
            (p.quantité - COALESCE(SUM(lc.quantite), 0)) as quantite_disponible
        FROM publication p
        LEFT JOIN ligne_commande lc ON p.id = lc.publication_id
        GROUP BY p.id, p.categorie, p.description, p.prix, p.image, p.quantité
        ORDER BY p.id DESC";

$query = $db->query($sql);
$articles = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   
    <h1 class="gestion-title">Gestion des Articles</h1>
    <div class="container">
        <div class="articles-container">
            <?php foreach($articles as $article): ?>
                <div class="gestion_article">
                    <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['categorie']); ?>">
                    <h4><?php echo htmlspecialchars($article['categorie']); ?></h4>
                    <div class="affiche_detail_article">
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($article['description']); ?></p>
                        <span class="prix"><?php echo htmlspecialchars($article['prix']); ?> f</span>
                        <p><strong>Quantité en stock:</strong> <?php echo htmlspecialchars($article['quantité']); ?></p>
                        <p><strong>Quantité commandée:</strong> <?php echo htmlspecialchars($article['quantite_commandee']); ?></p>
                        <p><strong>Quantité disponible:</strong> <span style="color: <?php echo $article['quantite_disponible'] > 0 ? 'green' : 'red'; ?>; font-weight: bold;"><?php echo htmlspecialchars($article['quantite_disponible']); ?></span></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="gestion-nav-links">
            <a href="publier.php">Retour à l'accueil</a>
        </div>
    </div>
   
    
</body>
</html>