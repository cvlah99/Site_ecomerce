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

$stmt_produits = $pdo->prepare("SELECT p.*, c.nom as nom_categorie FROM produits p 
                        JOIN categories c ON p.id_categorie = c.id_categorie 
                        ORDER BY p.id_produit ASC LIMIT 4");
$stmt_produits->execute();
$produits_vedettes = $stmt_produits->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Santé Beauté Naturelle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="acceulle.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="acceulle.php">
            <span class="logo-text">Soin<span class="logo-vital">Vital</span></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="acceulle.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="../catalogue/catalogue.php">Catalogue</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link fw-semibold dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        Catégories
                    </a>
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
                    <a class="nav-link fw-semibold" href="../apropos/apropos.php">À propos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="../contact/contact.php">Contact</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="nav-icon position-relative">
                    <i class="bi bi-search fs-5"></i>
                </a>
                <?php if(isset($_SESSION['user_id'])): ?>
                <a href="../panier/panier.php" class="nav-icon position-relative">
                    <i class="bi bi-bag fs-5"></i>
                    <span class="badge-cart"><?php echo $nb_panier; ?></span>
                </a>
                <a href="../profil/profil.php" class="text-success fw-semibold text-decoration-none">
                    <i class="bi bi-person-circle me-1"></i>Bonjour, <?php echo $_SESSION['user_prenom']; ?> !
                </a>
                <a href="../deconnexion/deconnexion.php" class="btn btn-outline-danger btn-sm px-3">Déconnexion</a>
                <?php else: ?>
                <a href="../connexion/connexion.php" class="btn btn-outline-success btn-sm px-3">Connexion</a>
                <a href="../inscription/inscription.php" class="btn btn-gold btn-sm px-3">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<section class="hero-section d-flex align-items-center">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 text-white" data-aos="fade-right" data-aos-duration="1000">
                <span class="badge-hero mb-3 d-inline-block">✨ Naturel & Certifié</span>
                <h1 class="hero-title mb-4">
                    Prenez soin de <br>
                    <span class="text-gold">votre beauté</span> <br>
                    & votre santé
                </h1>
                <p class="hero-subtitle mb-5">
                    Découvrez notre sélection premium de compléments alimentaires
                    et cosmétiques naturels, formulés pour sublimer votre bien-être au quotidien.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="../catalogue/catalogue.php" class="btn btn-gold btn-lg px-5 py-3">
                        Découvrir nos produits
                        <i class="bi bi-arrow-right ms-2"></i>
                    </a>
                    <a href="../apropos/apropos.php" class="btn btn-outline-light btn-lg px-5 py-3">
                        En savoir plus
                    </a>
                </div>
                <div class="row mt-5 g-4">
                    <div class="col-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="hero-stat">
                            <h3 class="text-gold fw-bold mb-0">500+</h3>
                            <p class="text-white-50 small mb-0">Produits</p>
                        </div>
                    </div>
                    <div class="col-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="hero-stat">
                            <h3 class="text-gold fw-bold mb-0">10K+</h3>
                            <p class="text-white-50 small mb-0">Clients</p>
                        </div>
                    </div>
                    <div class="col-4" data-aos="fade-up" data-aos-delay="300">
                        <div class="hero-stat">
                            <h3 class="text-gold fw-bold mb-0">100%</h3>
                            <p class="text-white-50 small mb-0">Naturel</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block text-center" data-aos="fade-left" data-aos-duration="1000">
                <div class="hero-animation-wrapper">
                    <div class="hero-circle-bg"></div>
                    <div class="hero-circle-sm circle-1"></div>
                    <div class="hero-circle-sm circle-2"></div>
                    <div class="hero-circle-sm circle-3"></div>
                    <div class="hero-center-card">
                        <div class="hero-icon-main">
                            <i class="bi bi-flower1"></i>
                        </div>
                        <div class="hero-floating-card card-1">
                            <i class="bi bi-capsule text-success me-2"></i>
                            <span>Compléments</span>
                        </div>
                        <div class="hero-floating-card card-2">
                            <i class="bi bi-droplet text-warning me-2"></i>
                            <span>Huiles</span>
                        </div>
                        <div class="hero-floating-card card-3">
                            <i class="bi bi-heart text-danger me-2"></i>
                            <span>Soins Corps</span>
                        </div>
                        <div class="hero-floating-card card-4">
                            <i class="bi bi-stars text-warning me-2"></i>
                            <span>100% Naturel</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-beige">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge">Nos Catégories</span>
            <h2 class="section-title mt-2">Explorez notre univers</h2>
            <p class="text-muted">Des produits soigneusement sélectionnés pour votre bien-être</p>
        </div>
        <div class="row g-4">
            <?php
            $icones = ['bi-capsule', 'bi-flower1', 'bi-droplet', 'bi-heart'];
            $i = 0;
            foreach($categories as $cat):
            ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?php echo ($i+1)*100; ?>">
                <div class="category-card text-center p-4">
                    <div class="category-icon mb-3">
                        <i class="bi <?php echo $icones[$i % 4]; ?> fs-1 text-success"></i>
                    </div>
                    <h5 class="fw-bold text-dark"><?php echo $cat['nom']; ?></h5>
                    <p class="text-muted small"><?php echo $cat['description']; ?></p>
                    <a href="../catalogue/catalogue.php?categorie=<?php echo $cat['id_categorie']; ?>" class="btn btn-outline-success btn-sm mt-2">Voir plus</a>
                </div>
            </div>
            <?php $i++; endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5" data-aos="fade-up">
            <div>
                <span class="section-badge">Sélection</span>
                <h2 class="section-title mt-2 mb-0">Produits Vedettes</h2>
            </div>
            <a href="../catalogue/catalogue.php" class="btn btn-outline-success">Voir tout <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-4">
            <?php foreach($produits_vedettes as $index => $produit): ?>
            <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="<?php echo ($index+1)*100; ?>">
                <div class="product-card">
                    <div class="product-img-wrapper">
                        <img src="<?php echo $produit['image']; ?>"
                             alt="<?php echo $produit['nom']; ?>" class="product-img">
                        <div class="product-overlay">
                            <a href="../details_produit/details_produit.php?id=<?php echo $produit['id_produit']; ?>" 
                               class="btn btn-gold btn-sm">Voir détails</a>
                        </div>
                    </div>
                    <div class="product-info p-3">
                        <span class="product-category"><?php echo $produit['nom_categorie']; ?></span>
                        <h6 class="product-name mt-1"><?php echo $produit['nom']; ?></h6>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <span class="product-price"><?php echo $produit['prix']; ?> MAD</span>
                            <div class="product-stars">
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-fill text-warning"></i>
                                <i class="bi bi-star-half text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="promo-banner py-5 my-3" data-aos="fade-up">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 text-white">
                <h2 class="fw-bold mb-2">Offre Spéciale — Livraison Gratuite</h2>
                <p class="mb-0 text-white-50">Pour toute commande supérieure à 300 MAD partout au Maroc 🇲🇦</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <a href="../catalogue/catalogue.php" class="btn btn-gold btn-lg px-5">Commander maintenant</a>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-beige">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge">Nos Avantages</span>
            <h2 class="section-title mt-2">Pourquoi choisir SoinVital ?</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 text-center" data-aos="flip-left" data-aos-delay="100">
                <div class="avantage-icon mb-3">
                    <i class="bi bi-patch-check fs-1 text-success"></i>
                </div>
                <h5 class="fw-bold">100% Naturel</h5>
                <p class="text-muted small">Produits formulés avec des ingrédients naturels certifiés</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center" data-aos="flip-left" data-aos-delay="200">
                <div class="avantage-icon mb-3">
                    <i class="bi bi-truck fs-1 text-success"></i>
                </div>
                <h5 class="fw-bold">Livraison Rapide</h5>
                <p class="text-muted small">Livraison partout au Maroc en 24 à 48 heures</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center" data-aos="flip-left" data-aos-delay="300">
                <div class="avantage-icon mb-3">
                    <i class="bi bi-shield-check fs-1 text-success"></i>
                </div>
                <h5 class="fw-bold">Paiement Sécurisé</h5>
                <p class="text-muted small">Paiement à la livraison disponible partout</p>
            </div>
            <div class="col-md-6 col-lg-3 text-center" data-aos="flip-left" data-aos-delay="400">
                <div class="avantage-icon mb-3">
                    <i class="bi bi-headset fs-1 text-success"></i>
                </div>
                <h5 class="fw-bold">Support 24/7</h5>
                <p class="text-muted small">Notre équipe est disponible pour vous aider</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge">Témoignages</span>
            <h2 class="section-title mt-2">Ce que disent nos clients</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-right" data-aos-delay="100">
                <div class="avis-card p-4">
                    <div class="mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                    </div>
                    <p class="text-muted fst-italic">"Les produits SoinVital sont exceptionnels ! Ma peau n'a jamais été aussi belle depuis que j'utilise leur crème à l'argan."</p>
                    <div class="d-flex align-items-center mt-3">
                        <div class="avis-avatar me-3">F</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Fatima B.</h6>
                            <small class="text-muted">Casablanca</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="avis-card p-4">
                    <div class="mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-half text-warning"></i>
                    </div>
                    <p class="text-muted fst-italic">"Je prends les compléments alimentaires depuis 3 mois et je me sens vraiment mieux. Livraison rapide et service excellent !"</p>
                    <div class="d-flex align-items-center mt-3">
                        <div class="avis-avatar me-3">Y</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Youssef M.</h6>
                            <small class="text-muted">Rabat</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-left" data-aos-delay="300">
                <div class="avis-card p-4">
                    <div class="mb-3">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                    </div>
                    <p class="text-muted fst-italic">"Qualité exceptionnelle et prix très raisonnables. Je recommande SoinVital à toute ma famille !"</p>
                    <div class="d-flex align-items-center mt-3">
                        <div class="avis-avatar me-3">S</div>
                        <div>
                            <h6 class="mb-0 fw-bold">Sara K.</h6>
                            <small class="text-muted">Tanger</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="newsletter-section py-5" data-aos="fade-up">
    <div class="container text-center">
        <h2 class="text-white fw-bold mb-2">Restez informé de nos offres</h2>
        <p class="text-white-50 mb-4">Inscrivez-vous à notre newsletter et recevez 10% de réduction sur votre première commande</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="email" class="form-control form-control-lg" placeholder="Votre adresse email...">
                    <button class="btn btn-gold px-4">S'inscrire</button>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer-section pt-5 pb-3">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="mb-3"><span class="logo-text-footer">Soin<span class="logo-vital-footer">Vital</span></span></div>
                <p class="text-muted small">SoinVital vous propose une sélection premium de compléments alimentaires et cosmétiques naturels pour prendre soin de vous au quotidien.</p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-4" data-aos="fade-up" data-aos-delay="200">
                <h6 class="footer-title mb-3">Liens rapides</h6>
                <ul class="footer-links">
                    <li><a href="acceulle.php">Accueil</a></li>
                    <a class="nav-link fw-semibold" href="../catalogue/catalogue.php">Catalogue</a>
                    <li><a href="../catalogue/catalogue.php">Promotions</a></li>
                    <li><a href="../apropos/apropos.php">À propos</a></li>
                    <li><a href="../contact/contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4" data-aos="fade-up" data-aos-delay="300">
                <h6 class="footer-title mb-3">Catégories</h6>
                <ul class="footer-links">
                    <li><a href="../catalogue/catalogue.php?categorie=1">Compléments</a></li>
                    <li><a href="../catalogue/catalogue.php?categorie=2">Cosmétiques</a></li>
                    <li><a href="../catalogue/catalogue.php?categorie=3">Huiles</a></li>
                    <li><a href="../catalogue/catalogue.php?categorie=4">Soins Corps</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4" data-aos="fade-up" data-aos-delay="400">
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
</script>
</body>
</html>