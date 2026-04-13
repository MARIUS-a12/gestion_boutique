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
$sql = $db->query("SELECT * FROM users");
$users = $sql->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>La liste de toutes les personnes qui ont créées un compte</h1>
    <table border="1">
        <tr>
            
            <th>Nom</th>
            <th>Contact</th>
        </tr>
        <?php
        foreach($users as $user){
            echo "<tr>";
            
            echo "<td>" . $user['nom'] . "</td>";
            echo "<td>" . $user['contact'] . "</td>";
            echo "</tr>";
        }
        ?>
</body>
</html>