<?php
session_start();
$nb_panier = 0;
require_once '../config/connexion_db.php';

// Fetch real cart count for the logged-in client
if(isset($_SESSION['id_client'])){
    $stmt_nb = $pdo->prepare("SELECT SUM(pp.quantite_produit) as total FROM panier_produits pp JOIN paniers pa ON pp.id_panier = pa.id_panier WHERE pa.id_client = :id_client");
    $stmt_nb->execute([':id_client' => $_SESSION['id_client']]);
    $result = $stmt_nb->fetch(PDO::FETCH_ASSOC);
    $nb_panier = $result['total'] ?? 0;
}
if(isset($_GET['categorie']) && is_numeric($_GET['categorie'])){
    $id_cat = intval($_GET['categorie']);
    $stmt = $pdo->prepare("SELECT p.*, c.nom as nom_categorie, 
                                 pr.pourcentage_remise, 
                                 (p.prix - (p.prix * (pr.pourcentage_remise / 100))) AS prix_remise
                            FROM produits p 
                            JOIN categories c ON p.id_categorie = c.id_categorie 
                            LEFT JOIN promotions pr ON p.id_produit = pr.id_produit 
                                  AND pr.statut = 'actif' 
                                  AND CURRENT_DATE >= pr.date_debut 
                                  AND CURRENT_DATE <= pr.date_fin
                            WHERE p.id_categorie = :id
                            ORDER BY p.date_ajout DESC");
    $stmt->execute([':id' => $id_cat]);
} else {
    $stmt = $pdo->prepare("SELECT p.*, c.nom as nom_categorie, 
                                 pr.pourcentage_remise, 
                                 (p.prix - (p.prix * (pr.pourcentage_remise / 100))) AS prix_remise
                            FROM produits p 
                            JOIN categories c ON p.id_categorie = c.id_categorie 
                            LEFT JOIN promotions pr ON p.id_produit = pr.id_produit 
                                  AND pr.statut = 'actif' 
                                  AND CURRENT_DATE >= pr.date_debut 
                                  AND CURRENT_DATE <= pr.date_fin
                            ORDER BY p.date_ajout DESC");
    $stmt->execute();
}
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
        <a class="navbar-brand d-flex align-items-center" href="../acceulle/acceulle.php">
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
                    <a class="nav-link fw-semibold active" href="catalogue.php">Catalogue</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link fw-semibold dropdown-toggle" href="#" data-bs-toggle="dropdown">Catégories</a>
                    <ul class="dropdown-menu border-0 shadow">
                        <?php foreach($categories as $cat): ?>
                        <li><a class="dropdown-item" href="catalogue.php?categorie=<?php echo $cat['id_categorie']; ?>"><i class="bi bi-tag me-2 text-success"></i><?php echo $cat['nom']; ?></a></li>
                        <?php endforeach; ?>
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
                <a href="../panier/panier.php" class="nav-icon position-relative">
                    <i class="bi bi-bag fs-5"></i>
                    <span class="badge-cart"><?php echo $nb_panier; ?></span>
                </a>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    
                    <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                        <a href="../admin/admin_dashboard.php" class="btn btn-dark btn-sm px-3 shadow-sm border-0">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    <?php elseif(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'livreur'): ?>
                        <a href="../livreur/livreur_dashboard.php" class="btn btn-dark btn-sm px-3 shadow-sm border-0">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    <?php endif; ?>

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

<!-- BANNIÈRE PROMO LIVRAISON -->
<div class="alert alert-dismissible fade show text-center mb-0 rounded-0 border-0 shadow-sm" role="alert" style="background-color: #f8f9fa; border-bottom: 2px solid var(--or) !important; padding: 12px 0; z-index: 1020; position: relative; margin-top: 75px;">
    <div class="container d-flex justify-content-center align-items-center">
        <i class="bi bi-truck fs-5 me-2" style="color: #2C4A3B;"></i>
        <span class="fw-semibold text-dark" style="font-size: 0.95rem;">
            Offre Spéciale : <span style="color: #2C4A3B;">Livraison Gratuite</span> pour toute commande supérieure à 300 MAD ! 🇲🇦
        </span>
    </div>
    <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Close" style="padding: 14px;"></button>
</div>
<!-- FIN BANNIÈRE PROMO -->

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
                         data-prix="<?php echo !empty($produit['pourcentage_remise']) ? $produit['prix_remise'] : $produit['prix']; ?>"
                         data-nom="<?php echo strtolower($produit['nom']); ?>"
                         data-aos="fade-up">
                        <div class="product-card">
                            <div class="product-img-wrapper position-relative">
                                <?php if (!empty($produit['pourcentage_remise'])): ?>
                                    <span class="badge bg-danger position-absolute top-0 start-0 m-2 shadow-sm" style="z-index: 2;">
                                        -<?php echo floatval($produit['pourcentage_remise']); ?>%
                                    </span>
                                <?php endif; ?>
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
                                    <div class="product-price d-flex flex-column">
                                        <?php if (!empty($produit['pourcentage_remise'])): ?>
                                            <span class="text-muted text-decoration-line-through small" style="font-size: 0.75rem;"><?php echo number_format($produit['prix'], 2); ?> MAD</span>
                                            <span class="text-success fw-bold"><?php echo number_format($produit['prix_remise'], 2); ?> MAD</span>
                                        <?php else: ?>
                                            <span class="fw-bold text-dark"><?php echo number_format($produit['prix'], 2); ?> MAD</span>
                                        <?php endif; ?>
                                    </div>
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
                    <li><a href="../acceulle/acceulle.php">Accueil</a></li>
                    <li><a href="catalogue.php">Catalogue</a></li>
                    <li><a href="../apropos/apropos.php">À propos</a></li>
                    <li><a href="../contact/contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4">
                <h6 class="footer-title mb-3">Catégories</h6>
                <ul class="footer-links">
                    <?php foreach($categories as $cat): ?>
                    <li><a href="../catalogue/catalogue.php?categorie=<?php echo $cat['id_categorie']; ?>"><?php echo $cat['nom']; ?></a></li>
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