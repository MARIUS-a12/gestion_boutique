
<?php
session_start();
require_once 'config.php';

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Enregistrer'])){
    $pdo = get_db_connection();
    $nom = trim($_POST['nom']);
    $contact = trim($_POST['contact']);
    
    $sql = "INSERT INTO users (nom, contact) VALUES (:nom, :contact)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':contact', $contact);
    if($stmt->execute()){
        // Récupérer l'ID de l'utilisateur créé
        $user_id = $pdo->lastInsertId();
        // Définir la session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['nom'] = $nom;
        // Rediriger vers le panier ou l'accueil
        $redirect = isset($_GET['redirect']) && $_GET['redirect'] === 'panier' ? 'panier.php' : 'index.php';
        header('Location: ' . $redirect);
        exit;
    } else {
        echo "Erreur lors de la création de l'utilisateur.";
    }
}
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
    <h1>créer un compte</h1>
    <p class="login-message">
        Pour passer votre commande, veuillez créer un compte avec votre nom et votre numéro de contact.
    </p>
    <div class="box">
        <form action="" class=" search-box login-user" method="POST">
            <input type="text" name="nom" id="" placeholder="nom complet..." required>
            <input type="tel" name="contact" id="" placeholder="contact..." required>
            <input type="submit" name="Enregistrer" id="" value="Enregistrer">
        </form>
        
    </div>
</body>
</html>