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

function filtrerProduits() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const prixMin = parseFloat(document.getElementById('prixMin').value) || 0;
    const prixMax = parseFloat(document.getElementById('prixMax').value) || Infinity;
    const categoriesChecked = Array.from(document.querySelectorAll('.filter-categorie:checked')).map(c => c.value);
    const trier = document.getElementById('trier').value;

    let produits = Array.from(document.querySelectorAll('.produit-item'));
    let count = 0;

    produits.forEach(produit => {
        const nom = produit.dataset.nom;
        const prix = parseFloat(produit.dataset.prix);
        const categorie = produit.dataset.categorie;

        const matchSearch = nom.includes(search);
        const matchPrix = prix >= prixMin && prix <= prixMax;
        const matchCategorie = categoriesChecked.includes('all') || categoriesChecked.includes(categorie);

        if (matchSearch && matchPrix && matchCategorie) {
            produit.style.display = 'block';
            count++;
        } else {
            produit.style.display = 'none';
        }
    });

    document.getElementById('nbProduits').textContent = count;
    document.getElementById('aucun-produit').style.display = count === 0 ? 'block' : 'none';

    if (trier !== 'default') {
        const grid = document.getElementById('produits-grid');
        const items = Array.from(grid.querySelectorAll('.produit-item')).filter(p => p.style.display !== 'none');

        items.sort((a, b) => {
            if (trier === 'prix-asc') return parseFloat(a.dataset.prix) - parseFloat(b.dataset.prix);
            if (trier === 'prix-desc') return parseFloat(b.dataset.prix) - parseFloat(a.dataset.prix);
            if (trier === 'nom') return a.dataset.nom.localeCompare(b.dataset.nom);
        });

        items.forEach(item => grid.appendChild(item));
    }
}

document.getElementById('searchInput').addEventListener('input', filtrerProduits);
document.getElementById('filtrerPrix').addEventListener('click', filtrerProduits);
document.getElementById('trier').addEventListener('change', filtrerProduits);

document.querySelectorAll('.filter-categorie').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (this.value === 'all' && this.checked) {
            document.querySelectorAll('.filter-categorie:not([value="all"])').forEach(c => c.checked = false);
        } else {
            document.getElementById('cat-all').checked = false;
        }
        filtrerProduits();
    });
});

document.getElementById('reinitialiser').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';
    document.getElementById('prixMin').value = '';
    document.getElementById('prixMax').value = '';
    document.getElementById('cat-all').checked = true;
    document.querySelectorAll('.filter-categorie:not([value="all"])').forEach(c => c.checked = false);
    document.getElementById('trier').value = 'default';
    filtrerProduits();
});