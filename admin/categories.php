<?php
session_start();
// Connect to the database
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

// Fetch categories and count the number of products linked to each
$query = "SELECT 
            c.id_categorie, 
            c.nom, 
            c.description,
            c.image,
            c.ordre_affichage,
            COUNT(p.id_produit) as nb_produits 
          FROM categories c 
          LEFT JOIN produits p ON c.id_categorie = p.id_categorie 
          GROUP BY 
            c.id_categorie, 
            c.nom,
            c.description,
            c.image,
            c.ordre_affichage
          ORDER BY c.ordre_affichage ASC, c.nom ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Catégories Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body>

    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="../acceulle/acceulle.php" class="text-decoration-none">
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
            <a href="categories.php" class="sidebar-link active">
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
            <a href="avis_clients.php" class="sidebar-link">
                <i class="bi bi-star"></i> Avis Clients
            </a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Gestion des Catégories</h5>
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
            <div class="admin-table-card">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Liste des Catégories</h5>
                    
                    <!-- Updated 'Nouvelle Catégorie' button with dynamic link -->
                    <a href="ajouter_categorie.php" class="text-decoration-none">
                        <button class="btn text-white fw-bold px-4" style="background: var(--vert); border: none; border-radius: 8px;">
                            <i class="bi bi-plus-lg me-2"></i>Nouvelle Catégorie
                        </button>
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                            <tr>
                                <th>Icone</th>
                                <th>Nom de la Catégorie</th>
                                <th>Produits Liés</th>
                                <th>ID Base de données</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($categories) > 0): ?>
                                <?php foreach($categories as $cat): ?>
                                    <?php 
                                        // Assigning a dynamic icon based on the category name for visual flair
                                        $icon = "bi-tags";
                                        $nom_lower = strtolower($cat['nom']);
                                        if (strpos($nom_lower, 'complément') !== false) $icon = "bi-capsule";
                                        if (strpos($nom_lower, 'cosmétique') !== false) $icon = "bi-flower1";
                                        if (strpos($nom_lower, 'huile') !== false) $icon = "bi-droplet";
                                    ?>
                                <tr>
                                    <td>
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                                            <i class="bi <?php echo $icon; ?> fs-5"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($cat['nom']); ?></h6>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            <?php echo $cat['nb_produits']; ?> Produit(s)
                                        </span>
                                    </td>
                                    <td><span class="text-muted small">ID: <?php echo $cat['id_categorie']; ?></span></td>
                                    
                                    <!-- Updated action buttons with dynamic links -->
                                    <td class="text-end">
                                        <a href="modifier_categorie.php?id=<?php echo $cat['id_categorie']; ?>" class="btn btn-sm btn-light text-primary me-1 shadow-sm" title="Modifier">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="supprimer_categorie.php?id=<?php echo $cat['id_categorie']; ?>" class="btn btn-sm btn-light text-danger shadow-sm" title="Supprimer" onclick="return confirm('Attention : supprimer cette catégorie peut affecter les produits liés. Continuer ?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>

                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Aucune catégorie trouvée.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>