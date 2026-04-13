
<?php
$username = "localhost";
$servername = "root";
$password = "";
$dbname = "boutique";
try{
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password)
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}catch (PDOExeception $e){

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
        <form action="" class=" search-box login-user">
            <input type="text" name="" id="" placeholder="nom...">
            <input type="number" name="" id="" placeholder="contact...">
            <input type="submit" name="" id="">
        </form>
    </div>
</body>
</html>