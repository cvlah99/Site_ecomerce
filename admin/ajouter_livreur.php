<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $vehicule = $_POST['vehicule'] ?? '';
    $zone_livraison = $_POST['zone_livraison'] ?? '';

    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($mot_de_passe) && !empty($vehicule)) {
        try {
            // 1. Insert core profile into utilisateurs table
            // Note: Using MD5 to match the exact format of the admin user you previously inserted
            $queryUtilisateur = "INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, date_inscription, statut) 
                                 VALUES (?, ?, ?, ?, MD5(?), CURDATE(), 'disponible')";
            $stmtU = $pdo->prepare($queryUtilisateur);
            $stmtU->execute([$nom, $prenom, $email, $telephone, $mot_de_passe]);

            // 2. Grab the newly generated id_utilisateur
            $id_utilisateur = $pdo->lastInsertId();

            // 3. Insert the delivery-specific details into the livreurs table
            $queryLivreur = "INSERT INTO livreurs (id_utilisateur, vehicule, zone_livraison, disponibilite, note_moyenne) 
                             VALUES (?, ?, ?, 1, 0)";
            $stmtL = $pdo->prepare($queryLivreur);
            $stmtL->execute([$id_utilisateur, $vehicule, $zone_livraison]);

            header("Location: livreurs.php?success=ajoute");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $erreur = "Cette adresse email est déjà utilisée.";
            } else {
                $erreur = "Erreur lors de l'enregistrement : " . $e->getMessage();
            }
        }
    } else {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Ajouter un Livreur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body>

    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="admin_dashboard.php" class="text-decoration-none">
                <span class="logo-text">Soin<span class="logo-vital">Vital</span></span>
            </a>
        </div>
        
        <div class="sidebar-menu">
            <div class="menu-label">Principal</div>
            <a href="admin_dashboard.php" class="sidebar-link">
                <i class="bi bi-grid-1x2-fill"></i> Tableau de bord
            </a>

            <div class="menu-label">E-Commerce</div>
            <a href="commandes.php" class="sidebar-link">
                <i class="bi bi-cart-check"></i> Commandes
            </a>
            <a href="produits.php" class="sidebar-link">
                <i class="bi bi-box-seam"></i> Produits
            </a>
            <a href="categories.php" class="sidebar-link">
                <i class="bi bi-tags"></i> Catégories
            </a>
            <a href="gestion_stock.php" class="sidebar-link">
                <i class="bi bi-boxes"></i> Gestion du Stock
            </a>

            <div class="menu-label">Utilisateurs & Logistique</div>
            <a href="clients.php" class="sidebar-link">
                <i class="bi bi-people"></i> Clients
            </a>
            <a href="livreurs.php" class="sidebar-link active">
                <i class="bi bi-truck"></i> Livreurs
            </a>

            <div class="menu-label">Marketing</div>
            <a href="promotions.php" class="sidebar-link">
                <i class="bi bi-percent"></i> Promotions
            </a>
            <a href="avis.php" class="sidebar-link">
                <i class="bi bi-star"></i> Avis Clients
            </a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Logistique & Livraison</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2 border-start ps-3 ms-2">
                    <div class="text-end d-none d-md-block">
                        <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Admin</p>
                        <p class="mb-0 text-muted" style="font-size: 0.8rem;">Superviseur</p>
                    </div>
                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold;">
                        A
                    </div>
                </div>
            </div>
        </header>

        <div class="admin-content">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="admin-table-card">
                        <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                            <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Ajouter un Livreur</h5>
                            <a href="livreurs.php" class="btn btn-light shadow-sm"><i class="bi bi-arrow-left me-2"></i>Retour à la flotte</a>
                        </div>
                        
                        <?php if(!empty($erreur)): ?>
                            <div class="alert alert-danger shadow-sm rounded-3"><i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $erreur; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row g-4">
                                <!-- Informations Utilisateur -->
                                <h6 class="fw-bold text-muted text-uppercase mb-0 mt-4 border-bottom pb-2">Informations Personnelles</h6>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Prénom *</label>
                                    <input type="text" name="prenom" class="form-control bg-light" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Nom *</label>
                                    <input type="text" name="nom" class="form-control bg-light" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Email *</label>
                                    <input type="email" name="email" class="form-control bg-light" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Téléphone *</label>
                                    <input type="text" name="telephone" class="form-control bg-light" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Mot de passe de connexion *</label>
                                    <input type="password" name="mot_de_passe" class="form-control bg-light" required>
                                </div>

                                <!-- Informations Logistiques -->
                                <h6 class="fw-bold text-muted text-uppercase mb-0 mt-5 border-bottom pb-2">Détails Logistiques</h6>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Type de Véhicule *</label>
                                    <select name="vehicule" class="form-select bg-light" required>
                                        <option value="">Sélectionner...</option>
                                        <option value="Moto">Moto</option>
                                        <option value="Fourgonnette">Fourgonnette</option>
                                        <option value="Voiture">Voiture</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Zone de Livraison *</label>
                                    <input type="text" name="zone_livraison" class="form-control bg-light" placeholder="Ex: Oujda - Centre" required>
                                </div>

                                <div class="col-12 mt-5 text-end">
                                    <a href="livreurs.php" class="btn btn-light px-4 me-2">Annuler</a>
                                    <button type="submit" class="btn text-white fw-bold px-5 shadow-sm" style="background: var(--vert); border: none;">
                                        <i class="bi bi-check-lg me-2"></i>Enregistrer le livreur
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>