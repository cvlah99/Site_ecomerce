<?php
session_start();
require_once '../config/connexion_db.php';

if(!isset($_SESSION['user_id']) || !isset($_SESSION['id_commande'])){
    header("location:../acceulle/acceulle.php");
    exit();
}

$id_commande = $_SESSION['id_commande'];
$id_client = $_SESSION['id_client'];

$stmt_cat = $pdo->prepare("SELECT * FROM categories ORDER BY ordre_affichage ASC");
$stmt_cat->execute();
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

$nb_panier = 0;

$stmt_commande = $pdo->prepare("SELECT * FROM commandes WHERE id_commande = :id AND id_client = :id_client");
$stmt_commande->execute([':id' => $id_commande, ':id_client' => $id_client]);
$commande = $stmt_commande->fetch(PDO::FETCH_ASSOC);

if(!$commande){
    header("location:../acceulle/acceulle.php");
    exit();
}

$stmt_produits = $pdo->prepare("SELECT cp.quantite, cp.prix_unitaire, p.nom, p.image
                                FROM commande_produits cp
                                JOIN produits p ON cp.id_produit = p.id_produit
                                WHERE cp.id_commande = :id_commande");
$stmt_produits->execute([':id_commande' => $id_commande]);
$produits = $stmt_produits->fetchAll(PDO::FETCH_ASSOC);

if(isset($_SESSION['succes'])){
    $succes = $_SESSION['succes'];
    unset($_SESSION['succes']);
}

unset($_SESSION['id_commande']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Commande Confirmée</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="confirmation.css" rel="stylesheet">
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

<section class="py-5 mt-5">
    <div class="container">
        <div class="confirmation-card text-center p-5 mx-auto">

            <div class="succes-icon mb-4">
                <i class="bi bi-check-circle-fill"></i>
            </div>

            <h2 class="confirmation-titre mb-2">Commande confirmée !</h2>
            <p class="text-muted mb-4">Merci <?php echo $_SESSION['user_prenom']; ?> ! Votre commande a été passée avec succès.</p>

            <div class="commande-badge mb-4">
                Commande #<?php echo $id_commande; ?>
            </div>

            <div class="commande-details text-start mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <i class="bi bi-calendar me-2 text-success"></i>
                            <strong>Date :</strong> <?php echo date('d/m/Y'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <i class="bi bi-geo-alt me-2 text-success"></i>
                            <strong>Livraison :</strong> <?php echo $commande['adresse_livraison']; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <i class="bi bi-truck me-2 text-success"></i>
                            <strong>Paiement :</strong> À la livraison
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-item">
                            <i class="bi bi-cash me-2 text-success"></i>
                            <strong>Total :</strong> <?php echo $commande['montant_total']; ?> MAD
                        </div>
                    </div>
                </div>
            </div>

            <div class="produits-liste text-start mb-4">
                <h6 class="fw-bold mb-3">Produits commandés :</h6>
                <?php foreach($produits as $produit): ?>
                <div class="produit-item d-flex align-items-center gap-3 mb-2">
                    <img src="<?php echo $produit['image']; ?>" alt="<?php echo $produit['nom']; ?>" class="produit-img">
                    <div class="flex-grow-1">
                        <span class="fw-semibold"><?php echo $produit['nom']; ?></span>
                        <span class="text-muted ms-2">x<?php echo $produit['quantite']; ?></span>
                    </div>
                    <span class="fw-bold"><?php echo $produit['prix_unitaire'] * $produit['quantite']; ?> MAD</span>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="statut-badge mb-4">
                <i class="bi bi-clock me-2"></i>En attente de traitement
            </div>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="../catalogue/catalogue.php" class="btn-continuer">
                    <i class="bi bi-bag me-2"></i>Continuer mes achats
                </a>
                <a href="../acceulle/acceulle.php" class="btn-accueil">
                    <i class="bi bi-house me-2"></i>Retour à l'accueil
                </a>
            </div>

        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>