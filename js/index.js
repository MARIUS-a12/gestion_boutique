const compte = document.querySelector('.compte');
const acheteurs = document.querySelectorAll('.acheter');
// fonction pour incrementer le compteur du panier
acheteurs.forEach(acheter => {
    acheter.addEventListener('click', () => {
        let count = parseInt(compte.getAttribute('data-counter')) || 0;
        count++;
        compte.setAttribute('data-counter', count);
    });
});

// =====================================
    // Gestion du panier avec localStorage
// =============
class panier{
    constructor(){
        this.clePanier = 'panier_utilisateur';
        this.jours_expiration = 7; // Durée de validité du panier en jours

    }
    // recuperer le panier depuis le localStorage
    obtenirPanier(){
        const panier = localStorage.getItem(this.clePanier);
        if(!panier){
            return { articless: [], timestamp: Date.now() };

        }
        return JSON.parse(panier);
    }
    // sauvegarder le panier dans le localStorage
    sauvegarderPanier(panier){
        localStorage.setItem(this.clePanier, JSON.stringify(panier));

    }
    //  vérifier sile panier a expiré
    estEpire(panier){
        const maintenant = Date.now();
        age_ms = maintenant - panier.timestamp;
        const age_jours = age_ms / (1000 * 60 * 60 * 24);
        return age_jours > this.jours_expiration;
    }
    // ajouter un article au panier
    ajouterArticle(publication_id,nom,quantite,prix,image){
        let panier = this.obtenirPanier();
        // vérifier l'expiration du panier
        if(this.estEpire(panier)){
            panier = { articless: [], timestamp: Date.now() };
            localStorage.removeItem(this.clePanier);
        }

        // cherche si l'article existe déjà dans le panier
        const articleExistant = panier.articless.find(article => article.publication_id == publication_id);
        if(articleExistant){
            articleExistant.quantite += quantite;
        }else{
            // sinon ajouter le nouvel article
            panier.articles.push({ publication_id,
                nom: nom,
                quantite: quantite,
                prix: prix,
                image: image
            });
        }
        // mettre à jour le timestamp
        panier.timestamp = Date.now();
        // sauvegarder le panier mis à jour
        this.sauvegarderPanier(panier);
        // tracer l'action au serveur (AJAX)
        this.tracerAction('ajouté', publication_id, 1);
        return true;
    }
    // modifier la quantité d'un article dans le panier
    modifierQuantite(publication_id, nouvelleQuantite){
        let panier = this.obtenirPanier();
        const article = panier.articles.find(article => article.publication_id == publication_id);
    if (article) {
        const ancienneQuantite = article.quantite;
        article.quantite = nouvelleQuantite;
        if(nouvelleQuantite <= 0){
            // si quantite est 0 ou négative, supprimer l'article du panier
            panier.articles = panier.articles.filter(article => article.publication_id != publication_id);
            this.tracerAction('supprimé', publication_id, ancienneQuantite);
        }else{
            this.tracerAction('quantité_modifiée', publication_id, nouvelleQuantite);
        }
        this.sauvegarderPanier(panier);
    }

    }
    // supprimer un article du panier
    supprimerArticle(publication_id){
        let panier = this.obtenirPanier();
        const article = panier.articles.find(article => article.publication_id == publication_id);
        if(article){
            panier.articles = panier.articles.filter(article => article.publication_id != publication_id);
            this.sauvegarderPanier(panier);
            this.tracerAction('supprimé', publication_id, article.quantite);
        }
    }
    // vider le panier
    viderPanier(){
        localStorage.removeItem(this.clePanier);
        // this.tracerAction('vidé', null, null);
    }
    // Envoter une requete au serveur pour tracer les actions du panier
    tracerAction(action, publication_id, quantite){
        fetch('panier.php', {
            method: 'POST',
            header: {
                'content-type': 'application/json'  
            },
            body: JSON.stringify({
                action: action,
                publication_id: publication_id,
                quantite: quantite 
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Action tracée enregistrée:', data);
        })
        .catch(error => console.error('Erreur Traçabilité:', error));
        
    }

    // retourner le nombre total d'articles dans le panier
    obtenirNombreArticles(){
        const panier = this.obtenirPanier();
        return panier.articles.length;
    }
    // retourner le temps restant avant l'expiration du panier
    joursRestant(){
        const panier = this.obtenirPanier();
        const maintenant = Date.now();
        const age_ms = maintenant - panier.timestamp;
        const age_jours = age_ms / (1000 * 60 * 60 * 24);
        const restant = this.jours_expiration - age_jours;
        return Math.max(0, Math.ceil(restant)); 
    }

}

// créer une instance du panier
const monPanier = new panier();
 