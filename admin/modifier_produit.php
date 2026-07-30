<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

$erreur = '';
$succes = '';

// 1. Fetch the product ID from the URL
$id_produit = $_GET['id'] ?? null;

if (!$id_produit) {
    header("Location: produits.php");
    exit();
}

// 2. Fetch categories for the dropdown
$stmt_cat = $pdo->query("SELECT * FROM categories ORDER BY nom ASC");
$categories = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);

// 3. Fetch existing product and stock details
$query_prod = "SELECT p.*, s.quantite_disponible, s.quantite_minimale 
               FROM produits p 
               JOIN stocks s ON p.id_produit = s.id_produit 
               WHERE p.id_produit = ?";
$stmt_prod = $pdo->prepare($query_prod);
$stmt_prod->execute([$id_produit]);
$produit = $stmt_prod->fetch(PDO::FETCH_ASSOC);

if (!$produit) {
    header("Location: produits.php"); // Product doesn't exist
    exit();
}

// 4. Handle the update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $id_categorie = $_POST['id_categorie'] ?? '';
    $prix = $_POST['prix'] ?? '';
    $description = $_POST['description'] ?? '';
    $marque = $_POST['marque'] ?? '';
    $image = $_POST['image'] ?? '';
    $quantite_disponible = $_POST['quantite_disponible'] ?? 0;
    $quantite_minimale = $_POST['quantite_minimale'] ?? 5;

    if (!empty($nom) && !empty($id_categorie) && !empty($prix)) {
        try {
            // Update the core product details
            $updateProduit = "UPDATE produits SET id_categorie = ?, nom = ?, description = ?, marque = ?, prix = ?, image = ? WHERE id_produit = ?";
            $pdo->prepare($updateProduit)->execute([$id_categorie, $nom, $description, $marque, $prix, $image, $id_produit]);
            
            // Update the stock details and stamp the modification date
            $updateStock = "UPDATE stocks SET quantite_disponible = ?, quantite_minimale = ?, date_mise_a_jour = CURDATE() WHERE id_produit = ?";
            $pdo->prepare($updateStock)->execute([$quantite_disponible, $quantite_minimale, $id_produit]);
            
            header("Location: produits.php?success=modifie");
            exit();
        } catch (PDOException $e) {
            $erreur = "Erreur lors de la mise à jour : " . $e->getMessage();
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
    <title>SoinVital - Modifier un Produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body>

    <aside class="admin-sidebar">
        <!-- Reusing your exact sidebar structure from ajouter_produit.php -->
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
            <a href="produits.php" class="sidebar-link active">
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
            <a href="livreurs.php" class="sidebar-link">
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
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Gestion des Produits</h5>
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
                            <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Modifier un Produit</h5>
                            <a href="produits.php" class="btn btn-light shadow-sm"><i class="bi bi-arrow-left me-2"></i>Retour au catalogue</a>
                        </div>
                        
                        <?php if(!empty($erreur)): ?>
                            <div class="alert alert-danger shadow-sm rounded-3"><i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $erreur; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Nom du produit *</label>
                                    <input type="text" name="nom" class="form-control bg-light" required value="<?php echo htmlspecialchars($produit['nom']); ?>">
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Catégorie *</label>
                                    <select name="id_categorie" class="form-select bg-light" required>
                                        <?php foreach($categories as $cat): ?>
                                            <option value="<?php echo $cat['id_categorie']; ?>" <?php echo ($cat['id_categorie'] == $produit['id_categorie']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['nom']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Prix (MAD) *</label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="prix" class="form-control bg-light" required value="<?php echo htmlspecialchars($produit['prix']); ?>">
                                        <span class="input-group-text bg-white">MAD</span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Description</label>
                                    <textarea name="description" class="form-control bg-light" rows="3"><?php echo htmlspecialchars($produit['description']); ?></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Marque</label>
                                    <input type="text" name="marque" class="form-control bg-light" value="<?php echo htmlspecialchars($produit['marque']); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">URL de l'image</label>
                                    <input type="url" name="image" class="form-control bg-light" value="<?php echo htmlspecialchars($produit['image']); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Stock Initial</label>
                                    <input type="number" name="quantite_disponible" class="form-control bg-light" min="0" value="<?php echo htmlspecialchars($produit['quantite_disponible']); ?>">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted small text-uppercase">Seuil d'alerte (Stock Min)</label>
                                    <input type="number" name="quantite_minimale" class="form-control bg-light" min="1" value="<?php echo htmlspecialchars($produit['quantite_minimale']); ?>">
                                </div>

                                <div class="col-12 mt-5 text-end">
                                    <a href="produits.php" class="btn btn-light px-4 me-2">Annuler</a>
                                    <button type="submit" class="btn text-white fw-bold px-5 shadow-sm" style="background: var(--vert); border: none;">
                                        <i class="bi bi-save me-2"></i>Mettre à jour
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