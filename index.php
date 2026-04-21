<?php
require_once 'config.php';

// Récupérer les articles avec pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12; // 12 articles par page
$offset = ($page - 1) * $limit;

try {
    $pdo = get_db_connection();
    $requete = $pdo->prepare("SELECT * FROM publication LIMIT :limit OFFSET :offset");
    $requete->bindValue(':limit', $limit, PDO::PARAM_INT);
    $requete->bindValue(':offset', $offset, PDO::PARAM_INT);
    $requete->execute();
    $articles = $requete->fetchAll(PDO::FETCH_ASSOC);

    // Compter le total pour la pagination
    $totalQuery = $pdo->query("SELECT COUNT(*) FROM publication");
    $totalArticles = $totalQuery->fetchColumn();
    $totalPages = ceil($totalArticles / $limit);
} catch (PDOException $e) {
    $articles = [];
    $totalPages = 1;
    if (DEV_MODE) {
        echo "<p>Erreur lors du chargement des articles: " . htmlspecialchars($e->getMessage()) . "</p>";
    } else {
        echo "<p>Erreur de chargement des articles.</p>";
    }
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
            <button type="submit"><i class="fas fa-search"></i></button>
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
                        <button type="button" class="acheter"
                            data-id="<?php echo intval($article['id']); ?>"
                            data-nom="<?php echo htmlspecialchars($article['nom'], ENT_QUOTES); ?>"
                            data-prix="<?php echo floatval($article['prix']); ?>"
                            data-image="<?php echo htmlspecialchars($article['image'], ENT_QUOTES); ?>">
                            🛒 Acheter
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="page-link">Précédent</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="page-link">Suivant</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <script src="js/index.js"></script>  
</body>
</html>