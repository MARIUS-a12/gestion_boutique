<?php
// identifiant de connexion
$host = "localhost";
$username = "root";
$password = "";
$dbname = "boutique";
$dev_mode = true;
// connexion a la base
try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // configure pdo pour qu'il lance des exceptions d'erreurs
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connexion réussie !";
} catch (PDOException $e) {
    // Si la connexion échoue, on attrape l'erreur
    if ($dev_mode) {
        // En développement : on affiche l'erreur complète
        die("Erreur de connexion : " . $e->getMessage());
    } else {
        // En production : message générique pour ne pas aider les pirates
        die("Un problème technique est survenu. Veuillez réessayer plus tard.");
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["validation"])) {
    $nom = trim($_POST["nom"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $prix = $_POST["prix"] ?? null;
    $categorie = trim($_POST["categorie"] ?? "");
    $quantité = $_POST["quantité"] ?? null;
    if (!isset($_FILES["image"]) || $_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
        echo "<p style='color:red;'>Veuillez sélectionner une image valide.</p>";
    } else {
        $image_name = basename($_FILES["image"]["name"]);
        $image_name = preg_replace('/[^A-Za-z0-9_.-]/', '_', $image_name);
        $destination = __DIR__ . "/uploads/" . $image_name;
        $db_path = "uploads/" . $image_name;

        if (!is_dir(__DIR__ . '/uploads')) {
            mkdir(__DIR__ . '/uploads', 0755, true);
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $destination)) {
            $requete = $db->prepare("INSERT INTO publication (nom, image, description, prix, categorie, quantité) VALUES(?, ?, ?, ?, ?, ?)");

            if ($requete->execute([$nom, $db_path, $description, $prix, $categorie, $quantité])) {
                echo "<p style='color:green;'>Succès complet !</p>";
            } else {
                echo "<p style='color:red;'>Erreur base de données</p>";
            }
        } else {
            echo "<p style='color:red;'>Erreur lors de l'upload de l'image</p>";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
     <header>
        <nav> 
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="gestion_article.html">Gestion des Articles</a></li>
                <li><a href="gestion_client.html">Gestion des Clients</a></li>
                <li><a href="gestion_commande.html">Gestion des Commandes</a></li>
                <li><a href="modif_article.html">Modification des Articles</a></li>
            </ul>
        </nav>
    </header>
    <h1>Publier un article</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="nom" placeholder="Nom de l'article à publier" required>
        <input type="file" name="image" accept="image/*" required>
        <textarea name="description" placeholder="Description"></textarea>
        <input type="number" name="prix" placeholder="Prix" required>
        <input type="number" name="quantité" placeholder="Quantité disponible" required>
        <select name="categorie" required>
            <option value="">Sélectionnez une catégorie</option>
            <option value="T-shirt">T-shirt</option>
            <option value="casquette">Casquette</option>
            <option value="telephone">Téléphone</option>
            <option value="vetement">Vêtement</option>
            <option value="parfum">Parfum</option>
            <option value="ordinateur">Ordinateur</option>
        </select>
        <input type="submit" name="validation" value="Publier">
    </form>
</body>
</html>