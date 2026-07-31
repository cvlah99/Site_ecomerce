<?php
session_start();
require_once '../config/connexion_db.php';
$stmt_cat = $pdo->prepare("SELECT * FROM categories ORDER BY ordre_affichage ASC");
$stmt_cat->execute();
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
$nb_panier = 0;
if(isset($_SESSION['id_client'])){
    $stmt_nb = $pdo->prepare("SELECT SUM(pp.quantite_produit) as total FROM panier_produits pp JOIN paniers pa ON pp.id_panier = pa.id_panier WHERE pa.id_client = :id_client");
    $stmt_nb->execute([':id_client' => $_SESSION['id_client']]);
    $result = $stmt_nb->fetch(PDO::FETCH_ASSOC);
    $nb_panier = $result['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - À propos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="apropos.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="../acceulle/acceulle.php">
            <span class="logo-text">Soin<span class="logo-vital">Vital</span></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="../acceulle/acceulle.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="../catalogue/catalogue.php">Catalogue</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link fw-semibold dropdown-toggle" href="#" data-bs-toggle="dropdown">Catégories</a>
                    <ul class="dropdown-menu border-0 shadow">
                        <?php foreach($categories as $cat): ?>
                        <li><a class="dropdown-item" href="../catalogue/catalogue.php?categorie=<?php echo $cat['id_categorie']; ?>">
                            <i class="bi bi-tag me-2 text-success"></i><?php echo $cat['nom']; ?>
                        </a></li>
                        <?php endforeach; ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../catalogue/catalogue.php"><i class="bi bi-star me-2 text-warning"></i>Tous les produits</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold active" href="#">À propos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="../contact/contact.php">Contact</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="../profil/profil.php" class="text-success fw-semibold text-decoration-none"><i class="bi bi-person-circle me-1"></i>Bonjour, <?php echo $_SESSION['user_prenom']; ?> !</a>
                    <a href="../deconnexion/deconnexion.php" class="btn btn-outline-danger btn-sm px-3">Déconnexion</a>
                <?php else: ?>
                    <a href="../connexion/connexion.php" class="btn btn-outline-success btn-sm px-3">Connexion</a>
                    <a href="../inscription/inscription.php" class="btn btn-gold btn-sm px-3">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<section class="apropos-banner">
    <div class="container text-center">
        <h1 class="banner-title" data-aos="fade-up">À propos de SoinVital</h1>
        <p class="banner-subtitle" data-aos="fade-up" data-aos-delay="100">Notre histoire, notre mission, nos valeurs</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6" data-aos="fade-right">
                <span class="section-badge">Notre Histoire</span>
                <h2 class="section-title mt-2">Qui sommes-nous ?</h2>
                <p class="text-muted mt-3">SoinVital est une entreprise marocaine spécialisée dans la vente de compléments alimentaires et de cosmétiques naturels. Fondée avec la passion du bien-être naturel, nous sélectionnons soigneusement chaque produit pour vous offrir le meilleur de la nature.</p>
                <p class="text-muted">Notre objectif est simple : vous aider à prendre soin de votre santé et de votre beauté avec des produits 100% naturels, efficaces et accessibles.</p>
                <div class="row mt-4 g-3">
                    <div class="col-6">
                        <div class="stat-card text-center p-3">
                            <h3 class="stat-number">500+</h3>
                            <p class="text-muted small mb-0">Produits</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card text-center p-3">
                            <h3 class="stat-number">10K+</h3>
                            <p class="text-muted small mb-0">Clients satisfaits</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <img src="https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=500&q=80"
                     alt="SoinVital" class="apropos-img">
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-beige">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge">Nos Valeurs</span>
            <h2 class="section-title mt-2">Ce qui nous distingue</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="valeur-card p-4">
                    <div class="valeur-icon mb-3">
                        <i class="bi bi-leaf fs-1"></i>
                    </div>
                    <h5 class="fw-bold">Naturel</h5>
                    <p class="text-muted small">Tous nos produits sont formulés avec des ingrédients naturels soigneusement sélectionnés</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="valeur-card p-4">
                    <div class="valeur-icon mb-3">
                        <i class="bi bi-shield-check fs-1"></i>
                    </div>
                    <h5 class="fw-bold">Qualité</h5>
                    <p class="text-muted small">Chaque produit est contrôlé et certifié pour garantir sa qualité et son efficacité</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 text-center" data-aos="fade-up" data-aos-delay="300">
                <div class="valeur-card p-4">
                    <div class="valeur-icon mb-3">
                        <i class="bi bi-heart fs-1"></i>
                    </div>
                    <h5 class="fw-bold">Bien-être</h5>
                    <p class="text-muted small">Votre santé et votre beauté sont notre priorité absolue au quotidien</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 text-center" data-aos="fade-up" data-aos-delay="400">
                <div class="valeur-card p-4">
                    <div class="valeur-icon mb-3">
                        <i class="bi bi-truck fs-1"></i>
                    </div>
                    <h5 class="fw-bold">Service</h5>
                    <p class="text-muted small">Livraison rapide partout au Maroc et service client disponible 6j/7</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container text-center">
        <div data-aos="fade-up">
            <span class="section-badge">Rejoignez-nous</span>
            <h2 class="section-title mt-2 mb-3">Prêt à prendre soin de vous ?</h2>
            <p class="text-muted mb-4">Découvrez notre catalogue et trouvez les produits qui correspondent à vos besoins</p>
            <a href="../catalogue/catalogue.php" class="btn btn-gold btn-lg px-5">
                Découvrir nos produits <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<footer class="footer-section pt-5 pb-3">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="mb-3"><span class="logo-text-footer">Soin<span class="logo-vital-footer">Vital</span></span></div>
                <p class="text-muted small">SoinVital vous propose une sélection premium de compléments alimentaires et cosmétiques naturels.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4">
                <h6 class="footer-title mb-3">Liens rapides</h6>
                <ul class="footer-links">
                    <li><a href="../acceulle/acceulle.php">Accueil</a></li>
                    <li><a href="../catalogue/catalogue.php">Catalogue</a></li>
                    <li><a href="../apropos/apropos.php">À propos</a></li>
                    <li><a href="../contact/contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4">
                <h6 class="footer-title mb-3">Contact</h6>
                <ul class="footer-links">
                    <li><i class="bi bi-geo-alt me-2 text-success"></i>Tanger, Maroc</li>
                    <li><i class="bi bi-telephone me-2 text-success"></i>+212 6XX XXX XXX</li>
                    <li><i class="bi bi-envelope me-2 text-success"></i>contact@soinvital.ma</li>
                    <li><i class="bi bi-clock me-2 text-success"></i>Lun - Sam : 9h - 18h</li>
                </ul>
            </div>
        </div>
        <hr class="mt-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <p class="text-muted small mb-0">© 2024 SoinVital. Tous droits réservés.</p>
            <p class="text-muted small mb-0">Fait avec ❤️ au Maroc 🇲🇦</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ duration: 800, once: true, offset: 100 });
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        if (window.scrollY > 50) navbar.classList.add('navbar-scrolled');
        else navbar.classList.remove('navbar-scrolled');
    });
</script>
</body>
</html>