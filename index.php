<?php
// identifiant de connexion
$host = "localhost";
$username = "root";
$password = "";
$dbname = "boutique";

// connexion a la base
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // configure pdo pour qu'il lance des exceptions d'erreurs
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les articles
try {
    $requete = $db->query("SELECT * FROM publication");
    $articles = $requete ? $requete->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (PDOException $e) {
    $articles = [];
    echo "<p>Erreur lors du chargement des articles: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loung</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo">Loung</div>
        <nav>
            <ul>
                <li><a href="#"><i class="fas fa-envelope"></i></a></li>
                <li><a href="#"><i class="fas fa-bell"></i></a></li> 
                <li>
                    <a href="panier.php">
                        <i class="fas fa-cart-shopping"></i>
                        <span class="badge-panier" style="display: none;">0</span>
                    </a>
                </li>
            </ul>  
        </nav>
        <button><a href="login_user.php"><i class="fas fa-sign-in-alt"></i></a></button>
    </header>
    
    <div class="container">
        <form action="" class="search-box">
            <input type="text" placeholder="rechercher...">
            <button type="submit"><a href="#"><i class="fas fa-search"></i></a></button>
        </form>
        <h1>Welcome to Loung</h1>
        
        <section>
            <?php foreach ($articles as $article): ?>
                <div class="card"> 
                    <div class="article">
                        <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['nom']); ?>">
                        <h4><?php echo htmlspecialchars($article['nom']); ?></h4>
                        <div class="detail_article">
                            <p><?php echo htmlspecialchars($article['description']); ?></p>
                            <p class="prix">prix: <?php echo htmlspecialchars($article['prix']); ?> f</p>
                        </div>
                        <button onclick="ajouterAuPanier(
                            <?php echo intval($article['id']); ?>,
                            '<?php echo addslashes(htmlspecialchars($article['nom'])); ?>',
                            1,
                            <?php echo floatval($article['prix']); ?>,
                            '<?php echo htmlspecialchars($article['image']); ?>'
                        )">🛒 Acheter</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </div>

    <script src="js/index.js"></script>  
</body>
</html>