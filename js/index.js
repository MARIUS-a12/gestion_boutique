// ============================================================================
// GESTION DU PANIER AVEC LOCALSTORAGE
// ============================================================================

class Panier {
    constructor() {
        this.clePanier = 'panier_utilisateur';
        this.jours_expiration = 7;
    }

    obtenirPanier() {
        const panier = localStorage.getItem(this.clePanier);
        if (!panier) {
            return { articles: [], timestamp: Date.now() };
        }
        return JSON.parse(panier);
    }

    sauvegarderPanier(panier) {
        localStorage.setItem(this.clePanier, JSON.stringify(panier));
    }

    estExpire(panier) {
        const maintenant = Date.now();
        const age_ms = maintenant - panier.timestamp;
        const age_jours = age_ms / (1000 * 60 * 60 * 24);
        return age_jours > this.jours_expiration;
    }

    ajouterArticle(publication_id, nom, quantite, prix, image) {
        let panier = this.obtenirPanier();

        if (this.estExpire(panier)) {
            panier = { articles: [], timestamp: Date.now() };
            localStorage.removeItem(this.clePanier);
        }

        const articleExistant = panier.articles.find(article => article.publication_id === publication_id);
        
        if (articleExistant) {
            articleExistant.quantite += quantite;
        } else {
            panier.articles.push({
                publication_id: publication_id,
                nom: nom,
                quantite: quantite,
                prix: prix,
                image: image
            });
        }

        panier.timestamp = Date.now();
        this.sauvegarderPanier(panier);
        this.tracerAction('ajouté', publication_id, quantite);
        return true;
    }

    modifierQuantite(publication_id, nouvelleQuantite) {
        let panier = this.obtenirPanier();
        const article = panier.articles.find(article => article.publication_id === publication_id);

        if (article) {
            const ancienneQuantite = article.quantite;
            article.quantite = nouvelleQuantite;

            if (nouvelleQuantite <= 0) {
                panier.articles = panier.articles.filter(article => article.publication_id !== publication_id);
                this.tracerAction('supprimé', publication_id, ancienneQuantite);
            } else {
                this.tracerAction('quantité_modifiée', publication_id, nouvelleQuantite);
            }

            this.sauvegarderPanier(panier);
        }
    }

    supprimerArticle(publication_id) {
        let panier = this.obtenirPanier();
        const article = panier.articles.find(article => article.publication_id === publication_id);

        if (article) {
            panier.articles = panier.articles.filter(article => article.publication_id !== publication_id);
            this.sauvegarderPanier(panier);
            this.tracerAction('supprimé', publication_id, article.quantite);
        }
    }

    viderPanier() {
        localStorage.removeItem(this.clePanier);
    }

    tracerAction(action, publication_id, quantite) {
        fetch('ajouter.panier.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: action,
                publication_id: publication_id,
                quantite: quantite
            })
        })
            .then(response => response.json())
            .then(data => {
                console.log('Action tracée:', data);
            })
            .catch(error => console.error('Erreur:', error));
    }

    obtenirNombreArticles() {
        const panier = this.obtenirPanier();
        return panier.articles.length;
    }

    calculerTotal() {
        const panier = this.obtenirPanier();
        return panier.articles.reduce((total, article) => {
            return total + (article.prix * article.quantite);
        }, 0).toFixed(2);
    }

    joursRestant() {
        const panier = this.obtenirPanier();
        const maintenant = Date.now();
        const age_ms = maintenant - panier.timestamp;
        const age_jours = age_ms / (1000 * 60 * 60 * 24);
        const restant = this.jours_expiration - age_jours;
        return Math.max(0, Math.ceil(restant));
    }
}

const monPanier = new Panier();

function initialiserBoutonsAcheter() {
    document.querySelectorAll('.acheter').forEach(button => {
        button.addEventListener('click', function() {
            const publication_id = parseInt(this.dataset.id, 10);
            const nom = this.dataset.nom;
            const quantite = 1;
            const prix = parseFloat(this.dataset.prix);
            const image = this.dataset.image;

            monPanier.ajouterArticle(publication_id, nom, quantite, prix, image);
            afficherNotification('✅ Article ajouté au panier!', 'success');
            mettreAJourCompteur();
        });
    });
}

function ajouterAuPanier(publication_id, nom, quantite, prix, image) {
    monPanier.ajouterArticle(publication_id, nom, quantite, prix, image);
    afficherNotification('✅ Article ajouté au panier!', 'success');
    mettreAJourCompteur();
}

// Exposer la fonction globalement pour les anciens onclick éventuels
window.ajouterAuPanier = ajouterAuPanier;

function afficherNotification(message, type = 'info') {
    const notif = document.createElement('div');
    notif.className = `notification ${type}`;
    notif.textContent = message;
    document.body.appendChild(notif);
    
    setTimeout(() => {
        notif.remove();
    }, 3000);
}

function mettreAJourCompteur() {
    const badge = document.querySelector('.badge-panier');
    if (badge) {
        const nombre = monPanier.obtenirNombreArticles();
        badge.textContent = nombre;
        badge.style.display = nombre > 0 ? 'flex' : 'none';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    mettreAJourCompteur();
    initialiserBoutonsAcheter();
    console.log('✅ Panier initialisé');
});