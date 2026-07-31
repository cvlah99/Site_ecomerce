AOS.init({
    duration: 800,
    once: true,
    offset: 100
});

window.addEventListener('scroll', function() {
    const navbar = document.getElementById('mainNavbar');
    if (window.scrollY > 50) {
        navbar.classList.add('navbar-scrolled');
    } else {
        navbar.classList.remove('navbar-scrolled');
    }
});

const btnDiminuer = document.getElementById('diminuer');
const btnAugmenter = document.getElementById('augmenter');
const quantiteEl = document.getElementById('quantite');
const quantiteInput = document.getElementById('quantite_input');

if(btnDiminuer && btnAugmenter) {
    btnDiminuer.addEventListener('click', function() {
        let q = parseInt(quantiteEl.textContent);
        if(q > 1) {
            quantiteEl.textContent = q - 1;
            if(quantiteInput) quantiteInput.value = q - 1;
        }
    });

    btnAugmenter.addEventListener('click', function() {
        let q = parseInt(quantiteEl.textContent);
        quantiteEl.textContent = q + 1;
        if(quantiteInput) quantiteInput.value = q + 1;
    });
}