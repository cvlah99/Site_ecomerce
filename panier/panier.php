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

$stmt_panier = $pdo->prepare("SELECT pa.id_panier FROM paniers pa WHERE pa.id_client = :id_client");
$stmt_panier->execute([':id_client' => $id_client]);
$panier = $stmt_panier->fetch(PDO::FETCH_ASSOC);

if(!$panier){
    $stmt_create = $pdo->prepare("INSERT INTO paniers (id_client, date_creation, montant_total) VALUES (:id_client, CURDATE(), 0)");
    $stmt_create->execute([':id_client' => $id_client]);
    $id_panier = $pdo->lastInsertId();
} else {
    $id_panier = $panier['id_panier'];
}

$stmt_produits = $pdo->prepare("SELECT pp.quantite_produit, p.id_produit, p.nom, p.prix, p.image, c.nom as nom_categorie
                                FROM panier_produits pp
                                JOIN produits p ON pp.id_produit = p.id_produit
                                JOIN categories c ON p.id_categorie = c.id_categorie
                                WHERE pp.id_panier = :id_panier");
$stmt_produits->execute([':id_panier' => $id_panier]);
$produits_panier = $stmt_produits->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
foreach($produits_panier as $item){
    $total += $item['prix'] * $item['quantite_produit'];
}

if(isset($_SESSION['succes'])){
    $succes = $_SESSION['succes'];
    unset($_SESSION['succes']);
}
if(isset($_SESSION['erreur'])){
    $erreur = $_SESSION['erreur'];
    unset($_SESSION['erreur']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Mon Panier</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="panier.css" rel="stylesheet">
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
                <a href="panier.php" class="nav-icon position-relative">
                    <i class="bi bi-bag fs-5"></i>
                    <span class="badge-cart"><?php echo count($produits_panier); ?></span>
                </a>
                <span class="text-success fw-semibold">Bonjour, <?php echo $_SESSION['user_prenom']; ?> !</span>
                <a href="../deconnexion/deconnexion.php" class="btn btn-outline-danger btn-sm px-3">Déconnexion</a>
            </div>
        </div>
    </div>
</nav>

<section class="panier-banner">
    <div class="container text-center">
        <h1 class="banner-title" data-aos="fade-up">Mon Panier</h1>
        <p class="banner-subtitle" data-aos="fade-up" data-aos-delay="100"><?php echo count($produits_panier); ?> article(s) dans votre panier</p>
    </div>
</section>

<section class="py-5">
    <div class="container">

        <?php if(isset($succes)): ?>
        <div class="message-succes mb-4">
            <i class="bi bi-check-circle me-2"></i><?php echo $succes; ?>
        </div>
        <?php endif; ?>

        <?php if(isset($erreur)): ?>
        <div class="message-erreur mb-4">
            <i class="bi bi-exclamation-circle me-2"></i><?php echo $erreur; ?>
        </div>
        <?php endif; ?>

        <?php if(count($produits_panier) > 0): ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="panier-card p-4">
                    <h5 class="panier-titre mb-4">Vos produits</h5>
                    <?php foreach($produits_panier as $item): ?>
                    <div class="panier-item d-flex align-items-center gap-3 mb-4">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['nom']; ?>" class="panier-img">
                        <div class="flex-grow-1">
                            <span class="panier-categorie"><?php echo $item['nom_categorie']; ?></span>
                            <h6 class="panier-nom mt-1"><?php echo $item['nom']; ?></h6>
                            <span class="panier-prix"><?php echo $item['prix']; ?> MAD</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <form action="panier_traitement.php" method="POST">
                                <input type="hidden" name="action" value="diminuer">
                                <input type="hidden" name="id_produit" value="<?php echo $item['id_produit']; ?>">
                                <input type="hidden" name="id_panier" value="<?php echo $id_panier; ?>">
                                <button type="submit" class="btn-quantite">-</button>
                            </form>
                            <span class="quantite-valeur"><?php echo $item['quantite_produit']; ?></span>
                            <form action="panier_traitement.php" method="POST">
                                <input type="hidden" name="action" value="augmenter">
                                <input type="hidden" name="id_produit" value="<?php echo $item['id_produit']; ?>">
                                <input type="hidden" name="id_panier" value="<?php echo $id_panier; ?>">
                                <button type="submit" class="btn-quantite">+</button>
                            </form>
                        </div>
                        <span class="panier-sous-total"><?php echo $item['prix'] * $item['quantite_produit']; ?> MAD</span>
                        <form action="panier_traitement.php" method="POST">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="id_produit" value="<?php echo $item['id_produit']; ?>">
                            <input type="hidden" name="id_panier" value="<?php echo $id_panier; ?>">
                            <button type="submit" class="btn-supprimer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="resume-card p-4">
                    <h5 class="panier-titre mb-4">Résumé</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Sous-total</span>
                        <span class="fw-semibold"><?php echo $total; ?> MAD</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Livraison</span>
                        <span class="text-success fw-semibold"><?php echo $total >= 300 ? 'Gratuite' : '30 MAD'; ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="fw-bold">Total</span>
                        <span class="total-prix"><?php echo $total >= 300 ? $total : $total + 30; ?> MAD</span>
                    </div>
                    <a href="../commande/commande.php" class="btn-commander w-100 text-center d-block">
                        <i class="bi bi-credit-card me-2"></i>Passer la commande
                    </a>
                    <a href="../catalogue/catalogue.php" class="btn-continuer w-100 text-center d-block mt-3">
                        <i class="bi bi-arrow-left me-2"></i>Continuer mes achats
                    </a>
                </div>
            </div>
        </div>

        <?php else: ?>
        <div class="text-center py-5" data-aos="fade-up">
            <i class="bi bi-bag fs-1 text-muted"></i>
            <h4 class="mt-3 text-muted">Votre panier est vide !</h4>
            <p class="text-muted">Découvrez nos produits et ajoutez-les à votre panier</p>
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
    AOS.init({ duration: 800, once: true, offset: 100 });
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        if (window.scrollY > 50) navbar.classList.add('navbar-scrolled');
        else navbar.classList.remove('navbar-scrolled');
    });
</script>
</body>
</html>