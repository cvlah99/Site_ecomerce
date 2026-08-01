<?php
session_start();
require_once '../config/connexion_db.php';

if(!isset($_SESSION['user_id'])){
    header("location:../connexion/connexion.php");
    exit();
}

$id_client = $_SESSION['id_client'];

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

$stmt_commandes = $pdo->prepare("SELECT c.*, 
                                  COUNT(cp.id_produit) as nb_produits
                                  FROM commandes c
                                  LEFT JOIN commande_produits cp ON c.id_commande = cp.id_commande
                                  WHERE c.id_client = :id_client
                                  GROUP BY c.id_commande
                                  ORDER BY c.date_commande DESC");
$stmt_commandes->execute([':id_client' => $id_client]);
$commandes = $stmt_commandes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Historique des commandes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="historique.css" rel="stylesheet">
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
                    <a class="nav-link fw-semibold" href="../apropos/apropos.php">À propos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link fw-semibold" href="../contact/contact.php">Contact</a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <a href="../panier/panier.php" class="nav-icon position-relative">
                    <i class="bi bi-bag fs-5"></i>
                    <span class="badge-cart"><?php echo $nb_panier; ?></span>
                </a>
                <a href="../profil/profil.php" class="text-success fw-semibold text-decoration-none">
                    <i class="bi bi-person-circle me-1"></i>Bonjour, <?php echo $_SESSION['user_prenom']; ?> !
                </a>
                <a href="../deconnexion/deconnexion.php" class="btn btn-outline-danger btn-sm px-3">Déconnexion</a>
            </div>
        </div>
    </div>
</nav>

<section class="historique-banner">
    <div class="container text-center">
        <h1 class="banner-title" data-aos="fade-up">Mes commandes</h1>
        <p class="banner-subtitle" data-aos="fade-up" data-aos-delay="100"><?php echo count($commandes); ?> commande(s) passée(s)</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <?php if(count($commandes) > 0): ?>
        <div class="row g-4">
            <?php foreach($commandes as $commande): ?>
            <div class="col-12" data-aos="fade-up">
                <div class="commande-card p-4">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="commande-numero">#<?php echo $commande['id_commande']; ?></div>
                            <small class="text-muted"><?php echo date('d/m/Y', strtotime($commande['date_commande'])); ?></small>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-1 fw-semibold">Adresse de livraison</p>
                            <p class="text-muted small mb-0"><?php echo $commande['adresse_livraison']; ?></p>
                        </div>
                        <div class="col-md-2 text-center">
                            <p class="mb-1 text-muted small">Produits</p>
                            <p class="fw-bold mb-0"><?php echo $commande['nb_produits']; ?> article(s)</p>
                        </div>
                        <div class="col-md-2 text-center">
                            <p class="mb-1 text-muted small">Total</p>
                            <p class="fw-bold text-success mb-0"><?php echo $commande['montant_total']; ?> MAD</p>
                        </div>
                        <div class="col-md-2 text-center">
                            <?php
                            $statut = $commande['statut'];
                            $classe = 'statut-attente';
                            if($statut === 'livré') $classe = 'statut-livre';
                            elseif($statut === 'en cours') $classe = 'statut-cours';
                            elseif($statut === 'annulé') $classe = 'statut-annule';
                            ?>
                            <span class="statut-badge <?php echo $classe; ?>"><?php echo ucfirst($statut); ?></span>
                        </div>
                        <div class="col-md-1 text-center">
                            <a href="details_commande.php?id=<?php echo $commande['id_commande']; ?>" class="btn-details">
                                <i class="bi bi-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5" data-aos="fade-up">
            <i class="bi bi-bag-x fs-1 text-muted"></i>
            <h4 class="mt-3 text-muted">Aucune commande passée !</h4>
            <p class="text-muted">Découvrez nos produits et passez votre première commande</p>
            <a href="../catalogue/catalogue.php" class="btn-commander mt-3 d-inline-block">
                <i class="bi bi-bag-plus me-2"></i>Découvrir nos produits
            </a>
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
<script>
    AOS.init({ duration: 800, once: true });
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        if (window.scrollY > 50) navbar.classList.add('navbar-scrolled');
        else navbar.classList.remove('navbar-scrolled');
    });
</script>
</body>
</html>