const compte = document.querySelector('.compte');
const acheter = document.querySelector('.acheter');
// fonction pour incrementer le compteur du panier
acheter.addEventListener('click', () => {
    let count = parseInt(compte.getAttribute('data-counter')) || 0;
    count++;
    compte.setAttribute('data-counter', count);
});