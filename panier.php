<?php
session_start();

// Vérifier que l'utilisateur est connecté
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login_user.php');
//     exit;
// }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛒 Mon Panier</h1>
            <p class="panier-info">Bienvenue <?php echo htmlspecialchars($_SESSION['nom'] ?? 'Utilisateur'); ?></p>
        </div>

        <!-- Avertissement d'expiration -->
        <div id="avertissementExpiration"></div>

        <!-- Contenu du panier -->
        <div id="contenuPanier"></div>
    </div>

    <script>
        var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>

    <script src="js/index.js"></script>

    <script>
        // Au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            afficherPanier();
        });

        // Fonction pour afficher le panier
        function afficherPanier() {
            const paniObj = monPanier.obtenirPanier();
            const container = document.getElementById('contenuPanier');
            const avertissement = document.getElementById('avertissementExpiration');

            // Vérifier l'expiration
            if (monPanier.estExpire(paniObj)) {
                monPanier.viderPanier();
                container.innerHTML = `
                    <div class="panier-vide">
                        <p>❌ Votre panier a expiré après 7 jours.</p>
                        <p>Veuillez recommencer vos achats.</p>
                        <a href="index.php" class="btn-retour">Retour au catalogue</a>
                    </div>
                `;
                return;
            }

            // Si panier vide
            if (paniObj.articles.length === 0) {
                container.innerHTML = `
                    <div class="panier-vide">
                        <p>📭 Votre panier est vide</p>
                        <a href="index.php" class="btn-retour">Continuer vos achats</a>
                    </div>
                `;
                return;
            }

            // Afficher l'avertissement d'expiration
            const joursRestants = monPanier.joursRestant();
            if (joursRestants <= 3) {
                avertissement.innerHTML = `
                    <div class="expiration-warning">
                        ⚠️ Attention: Votre panier expirera dans ${joursRestants} jour(s). Passez votre commande rapidement!
                    </div>
                `;
            }

            // Construire le HTML du panier
            let html = '<div class="panier-contenu">';

            paniObj.articles.forEach(article => {
                const sousTotal = (article.prix * article.quantite).toFixed(2);
                html += `
                    <div class="article-panier">
                        <img src="${article.image}" alt="${article.nom}" class="article-image" onerror="this.src='placeholder.jpg'">
                        <div class="article-infos">
                            <div class="article-nom">${article.nom}</div>
                            <div class="article-prix">${parseFloat(article.prix).toFixed(2)} FCFA</div>
                            <div class="quantite-controls">
                                <button onclick="diminuerQuantite(${article.publication_id})">➖</button>
                                <input type="number" class="quantite-input" id="quantite-${article.publication_id}" 
                                       value="${article.quantite}" min="1" onchange="changerQuantite(${article.publication_id}, this.value)">
                                <button onclick="augmenterQuantite(${article.publication_id})">➕</button>
                            </div>
                            <div class="article-sous-total">Sous-total: ${sousTotal} FCFA</div>
                            <button class="btn-supprimer" onclick="supprimerDuPanier(${article.publication_id})">🗑️ Supprimer</button>
                        </div>
                    </div>
                `;
            });

            // Résumé du panier
            const total = monPanier.calculerTotal();
            html += `
                <div class="panier-resume">
                    <div class="resume-ligne">
                        <span>Nombre d'articles:</span>
                        <span>${paniObj.articles.length}</span>
                    </div>
                    <div class="resume-total">
                        <span>Total:</span>
                        <span>${total} FCFA</span>
                    </div>
                    <div class="boutons-actions">
                        <button class="btn-commander" onclick="passerCommande()" id="btnCommander">
                            ✅ Commander
                        </button>
                        <a href="index.php" class="btn-continuer">Continuer les achats</a>
                    </div>
                </div>
            </div>
            `;

            container.innerHTML = html;
        }

        // Augmenter la quantité
        function augmenterQuantite(publication_id) {
            const input = document.getElementById(`quantite-${publication_id}`);
            input.value = parseInt(input.value) + 1;
            changerQuantite(publication_id, input.value);
        }

        // Diminuer la quantité
        function diminuerQuantite(publication_id) {
            const input = document.getElementById(`quantite-${publication_id}`);
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                changerQuantite(publication_id, input.value);
            }
        }

        // Changer la quantité
        function changerQuantite(publication_id, nouvelleQuantite) {
            nouvelleQuantite = parseInt(nouvelleQuantite);
            if (nouvelleQuantite < 1) {
                supprimerDuPanier(publication_id);
                return;
            }
            monPanier.modifierQuantite(publication_id, nouvelleQuantite);
            afficherPanier();
        }

        // Supprimer du panier
        function supprimerDuPanier(publication_id) {
            monPanier.supprimerArticle(publication_id);
            afficherNotification('Article supprimé du panier', 'success');
            afficherPanier();
            mettreAJourCompteur();
        }

        // Passer la commande
        function passerCommande() {
            // Vérifier si l'utilisateur est connecté
            if (!isLoggedIn) {
                afficherNotification('📋 Créez un compte pour passer votre commande', 'error');
                setTimeout(() => {
                    window.location.href = 'login_user.php?redirect=panier';
                }, 2500);
                return;
            }

            const paniObj = monPanier.obtenirPanier();

            if (paniObj.articles.length === 0) {
                afficherNotification('Le panier est vide', 'error');
                return;
            }

            const btnCommander = document.getElementById('btnCommander');
            const btnOriginal = btnCommander.textContent;
            btnCommander.disabled = true;
            btnCommander.textContent = '⏳ Création de la commande...';

            fetch('commande_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    articles: paniObj.articles,
                    total: monPanier.calculerTotal()
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    afficherNotification('✅ Commande créée avec succès!', 'success');
                    
                    monPanier.viderPanier();
                    mettreAJourCompteur();

                    setTimeout(() => {
                        window.location.href = `confirmation.php?commande=${data.commande_id}`;
                    }, 2000);
                } else {
                    afficherNotification(`❌ Erreur: ${data.message}`, 'error');
                    btnCommander.disabled = false;
                    btnCommander.textContent = btnOriginal;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                afficherNotification('❌ Erreur lors de la création de la commande', 'error');
                btnCommander.disabled = false;
                btnCommander.textContent = btnOriginal;
            });
        }
    </script>
</body>
</html>