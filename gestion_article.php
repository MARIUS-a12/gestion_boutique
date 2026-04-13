<?php
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'boutique';
try{
    $db =new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch(PDOException $e){
    die("Erreur de connexion : " . $e->getMessage());
}
$sql = $db->query("SELECT * FROM publication");
$articles = $sql->fetchAll(PDO::FETCH_ASSOC);
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
                        <p><strong>Quantité:</strong> <?php echo htmlspecialchars($article['quantité']); ?></p>
                        
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="gestion-nav-links">
            <a href="publier.php">Publier un nouvel article</a>
            <a href="publier.php">Retour à l'accueil</a>
        </div>
    </div>
   
    
</body>
</html>