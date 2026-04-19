<?php
$commande_id = isset($_GET['commande']) ? htmlspecialchars($_GET['commande']) : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de commande</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .confirmation-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            text-align: center;
        }
        .confirmation-container h1 {
            margin-bottom: 20px;
        }
        .confirmation-container p {
            font-size: 18px;
            color: #555;
            margin-bottom: 25px;
        }
        .confirmation-container .btn-home {
            display: inline-block;
            padding: 12px 24px;
            background: #27ae60;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }
        .confirmation-container .btn-home:hover {
            background: #219150;
        }
    </style>
</head>
<body>
    <div class="confirmation-container">
        <h1><i class="fas fa-check-circle"></i> Commande créée !</h1>
        <?php if ($commande_id): ?>
            <p>Votre commande a bien été enregistrée sous le numéro <strong><?php echo $commande_id; ?></strong>.</p>
        <?php else: ?>
            <p>Votre commande a bien été enregistrée.</p>
        <?php endif; ?>
        <a href="index.php" class="btn-home">Retour à la boutique</a>
    </div>
</body>
</html>
