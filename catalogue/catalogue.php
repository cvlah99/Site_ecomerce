<?php
session_start();
require_once '../config/connexion_db.php';
$stmt = $pdo->prepare("SELECT p.*, c.nom as nom_categorie 
                        FROM produits p 
                        JOIN categories c ON p.id_categorie = c.id_categorie 
                        ORDER BY p.date_ajout DESC");
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt_categories = $pdo->prepare("SELECT * FROM categories ORDER BY ordre_affichage ASC");
$stmt_categories->execute();
$categories = $stmt_categories->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Catalogue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="catalogue.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light fixed-top shadow-sm" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="../acceulle/acceulle.html">
            <span class="logo-text">Soin<span class="logo-vital">Vital</span></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="../acceulle/acceulle.html">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold active" href="#">Catalogue</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link fw-semibold dropdown-toggle" href="#" data-bs-toggle="dropdown">Catégories</a>
                    <ul class="dropdown-menu border-0 shadow">
                        <?php foreach($categories as $cat): ?>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-tag me-2 text-success"></i><?php echo $cat['nom']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="#">À propos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="#">Contact</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <a href="#" class="nav-icon position-relative">
                    <i class="bi bi-search fs-5"></i>
                </a>
                <a href="#" class="nav-icon position-relative">
                    <i class="bi bi-bag fs-5"></i>
                    <span class="badge-cart">0</span>
                </a>
                <a href="../connexion/connexion.php" class="btn btn-outline-success btn-sm px-3">Connexion</a>
                <a href="../inscription/inscription.php" class="btn btn-gold btn-sm px-3">S'inscrire</a>
            </div>
        </div>
    </div>
</nav>

<section class="catalogue-banner">
    <div class="container">
        <h1 class="banner-title">Notre Catalogue</h1>
        <p class="banner-subtitle">Découvrez tous nos produits naturels</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">

            <div class="col-lg-3">
                <div class="filter-card p-4">
                    <h5 class="filter-title mb-4">Filtrer par</h5>

                    <div class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher...">
                            <button class="btn btn-vert" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="filter-subtitle mb-3">Catégories</h6>
                        <div class="filter-options">
                            <div class="form-check mb-2">
                                <input class="form-check-input filter-categorie" type="checkbox" value="all" id="cat-all" checked>
                                <label class="form-check-label" for="cat-all">Toutes les catégories</label>
                            </div>
                            <?php foreach($categories as $cat): ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input filter-categorie" type="checkbox" 
                                       value="<?php echo $cat['nom']; ?>" 
                                       id="cat-<?php echo $cat['id_categorie']; ?>">
                                <label class="form-check-label" for="cat-<?php echo $cat['id_categorie']; ?>">
                                    <?php echo $cat['nom']; ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="filter-subtitle mb-3">Prix (MAD)</h6>
                        <div class="d-flex gap-2">
                            <input type="number" class="form-control form-control-sm" id="prixMin" placeholder="Min">
                            <input type="number" class="form-control form-control-sm" id="prixMax" placeholder="Max">
                        </div>
                        <button class="btn btn-vert btn-sm w-100 mt-2" id="filtrerPrix">Appliquer</button>
                    </div>

                    <button class="btn btn-outline-secondary btn-sm w-100" id="reinitialiser">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Réinitialiser
                    </button>
                </div>
            </div>

            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <p class="mb-0 text-muted">
                        <span id="nbProduits"><?php echo count($produits); ?></span> produits trouvés
                    </p>
                    <select class="form-select form-select-sm w-auto" id="trier">
                        <option value="default">Trier par défaut</option>
                        <option value="prix-asc">Prix croissant</option>
                        <option value="prix-desc">Prix décroissant</option>
                        <option value="nom">Nom A-Z</option>
                    </select>
                </div>

                <div class="row g-4" id="produits-grid">
                    <?php foreach($produits as $produit): ?>
                    <div class="col-md-6 col-lg-4 produit-item" 
                         data-categorie="<?php echo $produit['nom_categorie']; ?>"
                         data-prix="<?php echo $produit['prix']; ?>"
                         data-nom="<?php echo strtolower($produit['nom']); ?>"
                         data-aos="fade-up">
                        <div class="product-card">
                            <div class="product-img-wrapper">
                                <img src="<?php echo $produit['image']; ?>" 
                                     alt="<?php echo $produit['nom']; ?>" 
                                     class="product-img">
                                <div class="product-overlay">
                                    <a href="../details_produit/details_produit.php?id=<?php echo $produit['id_produit']; ?>" 
                                       class="btn btn-gold btn-sm">Voir détails</a>
                                </div>
                            </div>
                            <div class="product-info p-3">
                                <span class="product-category"><?php echo $produit['nom_categorie']; ?></span>
                                <h6 class="product-name mt-1"><?php echo $produit['nom']; ?></h6>
                                <p class="product-marque text-muted small mb-1"><?php echo $produit['marque']; ?></p>
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

                <div class="text-center mt-4" id="aucun-produit" style="display:none;">
                    <i class="bi bi-search fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Aucun produit trouvé</p>
                </div>
            </div>
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
                    <li><a href="../acceulle/acceulle.html">Accueil</a></li>
                    <li><a href="#">Catalogue</a></li>
                    <li><a href="#">À propos</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4">
                <h6 class="footer-title mb-3">Catégories</h6>
                <ul class="footer-links">
                    <?php foreach($categories as $cat): ?>
                    <li><a href="#"><?php echo $cat['nom']; ?></a></li>
                    <?php endforeach; ?>
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
<script src="catalogue.js"></script>
</body>
</html>