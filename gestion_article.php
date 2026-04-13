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
   
    <h1>Gestion des Articles</h1>
    <div class="container">
        <div class="card">
            <?php foreach($articles as $article): ?>
                <div class="article">
               
                    <h4><?php echo htmlspecialchars($article['categorie']); ?></h4>
                    <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="">
                    <P>prix: <?php echo htmlspecialchars($article['prix']); ?> f</P>
                    <p>Quantité: <?php echo htmlspecialchars($article['quantité']); ?></p> 
                
                </div>
            <?php endforeach; ?>
        </div>
        <a href="publier.php"> aller sur Publier un article</a>
    </div>
   
    
</body>
</html>