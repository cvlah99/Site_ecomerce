<?php
session_start();
require_once '../config/connexion_db.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
    header("location:../catalogue/catalogue.php");
    exit();
}

$id_produit = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT p.*, c.nom as nom_categorie 
                        FROM produits p 
                        JOIN categories c ON p.id_categorie = c.id_categorie 
                        WHERE p.id_produit = :id");
$stmt->execute([':id' => $id_produit]);
$produit = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$produit){
    header("location:../catalogue/catalogue.php");
    exit();
}

$stmt_stock = $pdo->prepare("SELECT quantite_disponible FROM stocks WHERE id_produit = :id");
$stmt_stock->execute([':id' => $id_produit]);
$stock = $stmt_stock->fetch(PDO::FETCH_ASSOC);

$stmt_avis = $pdo->prepare("SELECT a.*, u.nom, u.prenom 
                             FROM avis a 
                             JOIN clients c ON a.id_client = c.id_client
                             JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur
                             WHERE a.id_produit = :id
                             ORDER BY a.date_avis DESC");
$stmt_avis->execute([':id' => $id_produit]);
$avis = $stmt_avis->fetchAll(PDO::FETCH_ASSOC);

$stmt_cat = $pdo->prepare('SELECT * FROM categories ORDER BY ordre_affichage ASC');
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
    <title>SoinVital - <?php echo $produit['nom']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="details_produit.css" rel="stylesheet">
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

<section class="details-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../acceulle/acceulle.php">Accueil</a></li>
                <li class="breadcrumb-item"><a href="../catalogue/catalogue.php">Catalogue</a></li>
                <li class="breadcrumb-item active"><?php echo $produit['nom']; ?></li>
            </ol>
        </nav>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <div class="produit-image-wrapper">
                    <img src="<?php echo $produit['image']; ?>" 
                         alt="<?php echo $produit['nom']; ?>" 
                         class="produit-image">
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <span class="produit-categorie"><?php echo $produit['nom_categorie']; ?></span>
                <h1 class="produit-titre mt-2"><?php echo $produit['nom']; ?></h1>
                <p class="produit-marque text-muted">Par <strong><?php echo $produit['marque']; ?></strong></p>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="produit-stars">
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-fill text-warning"></i>
                        <i class="bi bi-star-half text-warning"></i>
                    </div>
                    <span class="text-muted small">(<?php echo count($avis); ?> avis)</span>
                </div>

                <div class="produit-prix mb-4"><?php echo $produit['prix']; ?> MAD</div>

                <div class="produit-description mb-4">
                    <p><?php echo $produit['description']; ?></p>
                </div>

                <div class="produit-stock mb-4">
                    <?php if($stock && $stock['quantite_disponible'] > 0): ?>
                        <span class="badge-stock-dispo">
                            <i class="bi bi-check-circle me-1"></i>
                            En stock (<?php echo $stock['quantite_disponible']; ?> disponibles)
                        </span>
                    <?php else: ?>
                        <span class="badge-stock-indispo">
                            <i class="bi bi-x-circle me-1"></i>
                            Rupture de stock
                        </span>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-3 flex-wrap">
                    <?php if($stock && $stock['quantite_disponible'] > 0): ?>
                    <div class="quantite-wrapper d-flex align-items-center gap-2">
                        <button class="btn-quantite" id="diminuer">-</button>
                        <span id="quantite">1</span>
                        <button class="btn-quantite" id="augmenter">+</button>
                    </div>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <form action="../panier/panier_traitement.php" method="POST" class="d-flex">
                        <input type="hidden" name="action" value="ajouter">
                        <input type="hidden" name="id_produit" value="<?php echo $produit['id_produit']; ?>">
                        <input type="hidden" name="quantite" id="quantite_input" value="1">
                        <button type="submit" class="btn btn-gold btn-lg px-5">
                            <i class="bi bi-bag-plus me-2"></i>Ajouter au panier
                        </button>
                    </form>
                    <?php else: ?>
                    <a href="../connexion/connexion.php" class="btn btn-gold btn-lg px-5">
                        <i class="bi bi-bag-plus me-2"></i>Ajouter au panier
                    </a>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-beige">
    <div class="container">
        <h3 class="section-titre mb-4" data-aos="fade-up">Avis clients</h3>
        <?php if(count($avis) > 0): ?>
            <div class="row g-4">
                <?php foreach($avis as $av): ?>
                <div class="col-md-6" data-aos="fade-up">
                    <div class="avis-card p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avis-avatar me-3"><?php echo strtoupper(substr($av['prenom'], 0, 1)); ?></div>
                            <div>
                                <h6 class="mb-0 fw-bold"><?php echo $av['prenom'] . ' ' . $av['nom']; ?></h6>
                                <small class="text-muted"><?php echo $av['date_avis']; ?></small>
                            </div>
                            <div class="ms-auto">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="bi bi-star-fill <?php echo $i <= $av['note'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <p class="text-muted fst-italic mb-0">"<?php echo $av['commentaire']; ?>"</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-4" data-aos="fade-up">
                <i class="bi bi-chat-dots fs-1 text-muted"></i>
                <p class="text-muted mt-2">Aucun avis pour ce produit</p>
                <a href="../connexion/connexion.php" class="btn btn-gold">Laisser un avis</a>
            </div>
        <?php endif; ?>
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
<script src="details_produit.js"></script>
</body>
</html>