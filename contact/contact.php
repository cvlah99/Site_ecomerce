<?php
session_start();
require_once '../config/connexion_db.php';
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
if(isset($_SESSION['erreur'])) {
    $erreur = $_SESSION['erreur'];
    unset($_SESSION['erreur']);
}
if(isset($_SESSION['succes'])) {
    $succes = $_SESSION['succes'];
    unset($_SESSION['succes']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="contact.css" rel="stylesheet">
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
                    <a class="nav-link fw-semibold active" href="#">Contact</a>
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

<section class="contact-banner">
    <div class="container text-center">
        <h1 class="banner-title" data-aos="fade-up">Contactez-nous</h1>
        <p class="banner-subtitle" data-aos="fade-up" data-aos-delay="100">Notre équipe est disponible pour vous aider</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">

            <div class="col-lg-4" data-aos="fade-right">
                <div class="info-card p-4">
                    <h4 class="info-title mb-4">Nos coordonnées</h4>

                    <div class="info-item mb-4">
                        <div class="info-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Adresse</h6>
                            <p class="text-muted mb-0">123 Rue Mohammed V<br>Tanger, Maroc</p>
                        </div>
                    </div>

                    <div class="info-item mb-4">
                        <div class="info-icon">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Téléphone</h6>
                            <p class="text-muted mb-0">+212 6XX XXX XXX</p>
                        </div>
                    </div>

                    <div class="info-item mb-4">
                        <div class="info-icon">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Email</h6>
                            <p class="text-muted mb-0">contact@soinvital.ma</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Horaires</h6>
                            <p class="text-muted mb-0">Lun - Sam : 9h - 18h</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <a href="#" class="social-icon"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-8" data-aos="fade-left">
                <div class="form-card p-4">
                    <h4 class="info-title mb-4">Envoyez-nous un message</h4>

                    <?php if(isset($erreur)): ?>
                    <div class="message-erreur">
                        <i class="bi bi-exclamation-circle me-2"></i><?php echo $erreur; ?>
                    </div>
                    <?php endif; ?>

                    <?php if(isset($succes)): ?>
                    <div class="message-succes">
                        <i class="bi bi-check-circle me-2"></i><?php echo $succes; ?>
                    </div>
                    <?php endif; ?>

                    <form id="contactForm" action="contact_traitement.php" method="POST" novalidate>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control"
                                       id="nom"
                                       name="nom"
                                       placeholder="Votre nom"
                                       minlength="3"
                                       maxlength="30"
                                       pattern="[a-zA-ZÀ-ÿ\s]{3,30}"
                                       required>
                                <ul class="conditions-list">
                                    <li id="nom-valide"><i class="bi bi-circle-fill"></i> Entre 3 et 30 caractères</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email"
                                       class="form-control"
                                       id="email"
                                       name="email"
                                       placeholder="exemple@email.com"
                                       pattern="[^\s@]+@[^\s@]+\.[^\s@]+"
                                       required>
                                <ul class="conditions-list">
                                    <li id="email-valide"><i class="bi bi-circle-fill"></i> Format email valide</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Sujet <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control"
                                   id="sujet"
                                   name="sujet"
                                   placeholder="Sujet de votre message"
                                   minlength="5"
                                   maxlength="200"
                                   required>
                            <ul class="conditions-list">
                                <li id="sujet-valide"><i class="bi bi-circle-fill"></i> Entre 5 et 200 caractères</li>
                            </ul>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control"
                                      id="message"
                                      name="message"
                                      rows="5"
                                      placeholder="Votre message..."
                                      minlength="20"
                                      required></textarea>
                            <ul class="conditions-list">
                                <li id="message-valide"><i class="bi bi-circle-fill"></i> Minimum 20 caractères</li>
                            </ul>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn-envoyer">
                                <i class="bi bi-send me-2"></i>Envoyer le message
                            </button>
                        </div>

                    </form>
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
<script src="contact.js"></script>
</body>
</html>