
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
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['Enregistrer'])){
    $nom = trim($_POST['nom']);
    $contact = trim($_POST['contact']);
    $sql = "INSERT INTO users (nom, contact) VALUES (:nom, :contact)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':contact', $contact);
    if($stmt->execute()){
        echo "Utilisateur créé avec succès.";
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
    <div class="box">
        <form action="" class=" search-box login-user" method="POST">
            <input type="text" name="nom" id="" placeholder="nom complet...">
            <input type="number" name="contact" id="" placeholder="contact...">
            <input type="submit" name="Enregistrer" id="">
        </form>
    </div>
</body>
</html>