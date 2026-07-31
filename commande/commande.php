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

$stmt_panier = $pdo->prepare("SELECT id_panier FROM paniers WHERE id_client = :id_client");
$stmt_panier->execute([':id_client' => $id_client]);
$panier = $stmt_panier->fetch(PDO::FETCH_ASSOC);

if(!$panier){
    header("location:../panier/panier.php");
    exit();
}

$id_panier = $panier['id_panier'];

$stmt_produits = $pdo->prepare("SELECT pp.quantite_produit, p.id_produit, p.nom, p.prix, p.image
                                FROM panier_produits pp
                                JOIN produits p ON pp.id_produit = p.id_produit
                                WHERE pp.id_panier = :id_panier");
$stmt_produits->execute([':id_panier' => $id_panier]);
$produits_panier = $stmt_produits->fetchAll(PDO::FETCH_ASSOC);

if(count($produits_panier) === 0){
    header("location:../panier/panier.php");
    exit();
}

$total = 0;
foreach($produits_panier as $item){
    $total += $item['prix'] * $item['quantite_produit'];
}
$livraison = $total >= 300 ? 0 : 30;
$total_final = $total + $livraison;

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
    <title>SoinVital - Passer la commande</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="commande.css" rel="stylesheet">
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
                <span class="text-success fw-semibold">Bonjour, <?php echo $_SESSION['user_prenom']; ?> !</span>
                <a href="../deconnexion/deconnexion.php" class="btn btn-outline-danger btn-sm px-3">Déconnexion</a>
            </div>
        </div>
    </div>
</nav>

<section class="commande-banner">
    <div class="container text-center">
        <h1 class="banner-title">Passer la commande</h1>
        <p class="banner-subtitle">Finalisez votre commande en quelques étapes</p>
    </div>
</section>

<section class="py-5">
    <div class="container">

        <?php if(isset($erreur)): ?>
        <div class="message-erreur mb-4">
            <i class="bi bi-exclamation-circle me-2"></i><?php echo $erreur; ?>
        </div>
        <?php endif; ?>

        <div class="row g-4">

            <div class="col-lg-7">
                <div class="commande-card p-4 mb-4">
                    <h5 class="commande-titre mb-4"><i class="bi bi-geo-alt me-2"></i>Adresse de livraison</h5>
                    <form id="commandeForm" action="commande_traitement.php" method="POST">
                        <input type="hidden" name="id_panier" value="<?php echo $id_panier; ?>">
                        <input type="hidden" name="total" value="<?php echo $total_final; ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nom" value="<?php echo $_SESSION['user_nom']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prénom <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="prenom" value="<?php echo $_SESSION['user_prenom']; ?>" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Adresse complète <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="adresse" placeholder="123 Rue Mohammed V" required>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <label class="form-label">Ville <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="ville" placeholder="Tanger" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="telephone" placeholder="06XXXXXXXX" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Note pour la commande</label>
                            <textarea class="form-control" name="note" rows="3" placeholder="Instructions spéciales..."></textarea>
                        </div>

                        <div class="mt-4">
                            <h5 class="commande-titre mb-3"><i class="bi bi-credit-card me-2"></i>Mode de paiement</h5>
                            <div class="paiement-option selected">
                                <input type="radio" name="paiement" value="livraison" id="paiement_livraison" checked>
                                <label for="paiement_livraison" class="d-flex align-items-center gap-3">
                                    <i class="bi bi-truck fs-4 text-success"></i>
                                    <div>
                                        <strong>Paiement à la livraison</strong>
                                        <p class="mb-0 text-muted small">Payez en espèces à la réception de votre commande</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn-confirmer mt-4 w-100">
                            <i class="bi bi-check-circle me-2"></i>Confirmer la commande
                        </button>

                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="commande-card p-4">
                    <h5 class="commande-titre mb-4"><i class="bi bi-bag me-2"></i>Résumé de la commande</h5>
                    <?php foreach($produits_panier as $item): ?>
                    <div class="resume-item d-flex align-items-center gap-3 mb-3">
                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['nom']; ?>" class="resume-img">
                        <div class="flex-grow-1">
                            <p class="mb-0 fw-semibold small"><?php echo $item['nom']; ?></p>
                            <small class="text-muted">Quantité: <?php echo $item['quantite_produit']; ?></small>
                        </div>
                        <span class="fw-bold"><?php echo $item['prix'] * $item['quantite_produit']; ?> MAD</span>
                    </div>
                    <?php endforeach; ?>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Sous-total</span>
                        <span><?php echo $total; ?> MAD</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Livraison</span>
                        <span class="<?php echo $livraison == 0 ? 'text-success' : ''; ?>"><?php echo $livraison == 0 ? 'Gratuite' : $livraison . ' MAD'; ?></span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total</strong>
                        <strong class="total-prix"><?php echo $total_final; ?> MAD</strong>
                    </div>
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
</body>
</html>