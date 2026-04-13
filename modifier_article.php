<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "boutique";
try{
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch (PDOException $e){
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement de la suppression
if(isset($_GET['action']) && $_GET['action'] == 'supprimer' && isset($_GET['id'])){
    $id = $_GET['id'];
    $stmt = $db->prepare("DELETE FROM publication WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: modifier_article.php");
    exit();
}

// Traitement de la modification
$article_a_modifier = null;
if(isset($_POST['update_article'])){
    $id = $_POST['id'];
    $categorie = $_POST['categorie'];
    $prix = $_POST['prix'];
    $quantite = $_POST['quantité'];
    $description = $_POST['description'];
    
    $stmt = $db->prepare("UPDATE publication SET categorie = ?, prix = ?, quantité = ?, description = ? WHERE id = ?");
    $stmt->execute([$categorie, $prix, $quantite, $description, $id]);
    header("Location: modifier_article.php");
    exit();
}

// Affichage du formulaire de modification
if(isset($_GET['action']) && $_GET['action'] == 'modifier' && isset($_GET['id'])){
    $stmt = $db->prepare("SELECT * FROM publication WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $article_a_modifier = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupération de tous les articles
$sql = $db->query("SELECT * FROM publication");
$articles = $sql->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un article</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Modifier un article</h1>
    
    <?php if($article_a_modifier): ?>
        <div class="modification-form">
            <h2>Modification de l'article</h2>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $article_a_modifier['id']; ?>">
                
                <div class="form-group">
                    <label for="categorie">Catégorie:</label>
                    <input type="text" id="categorie" name="categorie" value="<?php echo htmlspecialchars($article_a_modifier['categorie']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="prix">Prix:</label>
                    <input type="number" id="prix" name="prix" value="<?php echo htmlspecialchars($article_a_modifier['prix']); ?>" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="quantite">Quantité:</label>
                    <input type="number" id="quantite" name="quantité" value="<?php echo htmlspecialchars($article_a_modifier['quantité']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($article_a_modifier['description']); ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="update_article" class="form-submit">Enregistrer</button>
                    <a href="modifier_article.php" class="form-cancel">Annuler</a>
                </div>
            </form>
        </div>
    <?php endif; ?>
    <?php if(count($articles) > 0): ?>
        <div class="articles-container">
            <?php foreach($articles as $article): ?>
                <div class="article-card">
                    <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['categorie']); ?>">
                    
                    <div class="article-info">
                        <strong>Catégorie:</strong> <?php echo htmlspecialchars($article['categorie']); ?>
                    </div>
                    
                    <div class="article-info">
                        <strong>Prix:</strong> <?php echo htmlspecialchars($article['prix']); ?> f
                    </div>
                    
                    <div class="article-info">
                        <strong>Quantité:</strong> <?php echo htmlspecialchars($article['quantité']); ?>
                    </div>
                    
                    <div class="article-info">
                        <strong>Description:</strong> <?php echo htmlspecialchars($article['description']); ?>
                    </div>
                    
                    <div class="actions">
                        <a href="modifier_article.php?action=modifier&id=<?php echo $article['id']; ?>" class="btn btn-modifier">Modifier</a>
                        <a href="modifier_article.php?action=supprimer&id=<?php echo $article['id']; ?>" class="btn btn-supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article?');">Supprimer</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-articles">
            <p>Aucun article trouvé.</p>
        </div>
    <?php endif; ?>
    
    <div class="nav-links">
        <a href="publier.php" class="btn btn-modifier">Publier un nouvel article</a>
        <a href="index.php" class="btn btn-modifier">Retour à l'accueil</a>
    </div>

</body>
</html>