<?php
session_start();
require_once '../config/connexion_db.php';

if(!isset($_SESSION['user_id'])){
    header("location:../connexion/connexion.php");
    exit();
}

$id_utilisateur = $_SESSION['user_id'];
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

$stmt_user = $pdo->prepare("SELECT u.*, c.adresse, c.ville, c.code_postal, c.pays, c.date_naissance, c.genre
                             FROM utilisateurs u
                             JOIN clients c ON u.id_utilisateur = c.id_utilisateur
                             WHERE u.id_utilisateur = :id");
$stmt_user->execute([':id' => $id_utilisateur]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

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
    <title>SoinVital - Mon Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="profil.css" rel="stylesheet">
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

<section class="profil-banner">
    <div class="container text-center">
        <div class="avatar-circle mb-3">
            <?php echo strtoupper(substr($user['prenom'], 0, 1)); ?>
        </div>
        <h1 class="banner-title"><?php echo $user['prenom'] . ' ' . $user['nom']; ?></h1>
        <p class="banner-subtitle">Membre depuis le <?php echo date('d/m/Y', strtotime($user['date_inscription'])); ?></p>
    </div>
</section>

<section class="py-5">
    <div class="container">

        <?php if(isset($succes)): ?>
        <div class="message-succes mb-4"><i class="bi bi-check-circle me-2"></i><?php echo $succes; ?></div>
        <?php endif; ?>

        <?php if(isset($erreur)): ?>
        <div class="message-erreur mb-4"><i class="bi bi-exclamation-circle me-2"></i><?php echo $erreur; ?></div>
        <?php endif; ?>

        <div class="row g-4">

            <div class="col-lg-8">
                <div class="profil-card p-4 mb-4">
                    <h5 class="profil-titre mb-4"><i class="bi bi-person me-2"></i>Informations personnelles</h5>
                    <form action="profil_traitement.php" method="POST">
                        <input type="hidden" name="action" value="infos">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control" name="nom" value="<?php echo $user['nom']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control" name="prenom" value="<?php echo $user['prenom']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?php echo $user['email']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" name="telephone" value="<?php echo $user['telephone']; ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn-sauvegarder mt-3">
                            <i class="bi bi-check me-2"></i>Sauvegarder
                        </button>
                    </form>
                </div>

                <div class="profil-card p-4 mb-4">
                    <h5 class="profil-titre mb-4"><i class="bi bi-geo-alt me-2"></i>Adresse de livraison</h5>
                    <form action="profil_traitement.php" method="POST">
                        <input type="hidden" name="action" value="adresse">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Adresse</label>
                                <input type="text" class="form-control" name="adresse" value="<?php echo $user['adresse'] ?? ''; ?>" placeholder="123 Rue Mohammed V">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Ville</label>
                                <input type="text" class="form-control" name="ville" value="<?php echo $user['ville'] ?? ''; ?>" placeholder="Tanger">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Code postal</label>
                                <input type="text" class="form-control" name="code_postal" value="<?php echo $user['code_postal'] ?? ''; ?>" placeholder="90000">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Pays</label>
                                <input type="text" class="form-control" name="pays" value="<?php echo $user['pays'] ?? 'Maroc'; ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn-sauvegarder mt-3">
                            <i class="bi bi-check me-2"></i>Sauvegarder
                        </button>
                    </form>
                </div>

                <div class="profil-card p-4">
                    <h5 class="profil-titre mb-4"><i class="bi bi-lock me-2"></i>Modifier le mot de passe</h5>
                    <form action="profil_traitement.php" method="POST">
                        <input type="hidden" name="action" value="mot_de_passe">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Mot de passe actuel</label>
                                <input type="password" class="form-control" name="ancien_mdp" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" class="form-control" name="nouveau_mdp" minlength="8" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirmer le mot de passe</label>
                                <input type="password" class="form-control" name="confirmer_mdp" minlength="8" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-sauvegarder mt-3">
                            <i class="bi bi-lock me-2"></i>Modifier le mot de passe
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="profil-card p-4 text-center mb-4">
                    <div class="avatar-large mb-3">
                        <?php echo strtoupper(substr($user['prenom'], 0, 1)); ?>
                    </div>
                    <h5 class="fw-bold"><?php echo $user['prenom'] . ' ' . $user['nom']; ?></h5>
                    <p class="text-muted small"><?php echo $user['email']; ?></p>
                    <span class="statut-badge">✅ Compte actif</span>
                </div>

                <div class="profil-card p-4">
                    <h6 class="profil-titre mb-3">Liens rapides</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="../historique/historique.php" class="lien-rapide">
                                <i class="bi bi-clock-history me-2"></i>Historique commandes
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="../panier/panier.php" class="lien-rapide">
                                <i class="bi bi-bag me-2"></i>Mon panier
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="../catalogue/catalogue.php" class="lien-rapide">
                                <i class="bi bi-grid me-2"></i>Catalogue
                            </a>
                        </li>
                        <li>
                            <a href="../deconnexion/deconnexion.php" class="lien-rapide text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Déconnexion
                            </a>
                        </li>
                    </ul>
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