<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

// Fetch stock levels joined with product info, ordered by lowest stock first
$query = "SELECT 
            p.id_produit, 
            p.nom, 
            p.image, 
            p.prix,
            c.nom as nom_categorie, 
            s.quantite_disponible,
            s.quantite_minimale,
            s.date_mise_a_jour
          FROM produits p 
          JOIN stocks s ON p.id_produit = s.id_produit
          LEFT JOIN categories c ON p.id_categorie = c.id_categorie
          ORDER BY s.quantite_disponible ASC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Gestion du Stock</title>
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
            <a href="categories.php" class="sidebar-link">
                <i class="bi bi-tags"></i> Catégories
            </a>
            <a href="gestion_stock.php" class="sidebar-link active">
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
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Inventaire & Stock</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light position-relative">
                    <i class="bi bi-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </button>
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
                    <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Suivi des Stocks</h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary fw-bold px-3" style="border-radius: 8px;">
                            <i class="bi bi-arrow-clockwise me-2"></i>Actualiser
                        </button>
                        <button class="btn text-white fw-bold px-4" style="background: var(--or); border: none; border-radius: 8px;">
                            <i class="bi bi-plus-lg me-2"></i>Entrée de Stock
                        </button>
                    </div>
                </div>

                <div class="row mb-4 g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 bg-light" placeholder="Rechercher un produit ou une référence...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select bg-light">
                            <option value="">Toutes les catégories</option>
                            <option value="cosmetiques">Cosmétiques</option>
                            <option value="complements">Compléments</option>
                            <option value="huiles">Huiles Essentielles</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select bg-light border-danger text-danger">
                            <option value="">Tous les états</option>
                            <option value="rupture">En rupture</option>
                            <option value="faible">Stock faible (< 10)</option>
                        </select>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                            <tr>
                                <th>Produit</th>
                                <th>Catégorie</th>
                                <th>Quantité</th>
                                <th>État du Stock</th>
                                <th>Dernière MAJ</th>
                                <th class="text-end">Ajuster</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($stocks) > 0): ?>
                                <?php foreach($stocks as $item): ?>
                                    <?php 
                                        $qty = $item['quantite_disponible'];
                                        
                                        // Visual Logic for Stock Levels
                                        if ($qty == 0) {
                                            $badgeClass = 'bg-danger bg-opacity-10 text-danger';
                                            $statusText = 'Rupture de stock';
                                            $icon = 'bi-x-octagon-fill';
                                            $textColor = 'text-danger fw-bold';
                                        } elseif ($qty <= 10) {
                                            $badgeClass = 'bg-warning bg-opacity-10 text-warning';
                                            $statusText = 'Stock faible';
                                            $icon = 'bi-exclamation-triangle-fill';
                                            $textColor = 'text-warning fw-bold';
                                        } else {
                                            $badgeClass = 'bg-success bg-opacity-10 text-success';
                                            $statusText = 'En stock';
                                            $icon = 'bi-check-circle-fill';
                                            $textColor = 'text-dark fw-bold';
                                        }
                                    ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Produit" class="rounded shadow-sm me-3" style="width: 45px; height: 45px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;"><?php echo htmlspecialchars($item['nom']); ?></h6>
                                                <small class="text-muted">Réf: PROD-<?php echo str_pad($item['id_produit'], 4, '0', STR_PAD_LEFT); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-muted"><?php echo htmlspecialchars($item['nom_categorie']); ?></span></td>
                                    <td>
                                        <span class="<?php echo $textColor; ?> fs-6"><?php echo $qty; ?></span>
                                        <span class="text-muted small ms-1">unités</span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $badgeClass; ?> px-3 py-2 rounded-pill">
                                            <i class="bi <?php echo $icon; ?> me-1"></i><?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td><span class="text-muted small"><?php echo $item['date_mise_a_jour'] ?? 'Aujourd\'hui'; ?></span></td>
                                    
                                    <!-- Inline Stock Adjustment Form -->
                                    <td class="text-end">
                                        <form action="mettre_a_jour_stock.php" method="POST" class="d-inline-flex align-items-center justify-content-end gap-1">
                                            <input type="hidden" name="id_produit" value="<?php echo $item['id_produit']; ?>">
                                            <div class="input-group input-group-sm w-auto shadow-sm rounded">
                                                <input type="number" name="quantite_disponible" class="form-control text-center bg-white" value="<?php echo $qty; ?>" min="0" style="max-width: 65px;" required>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-success shadow-sm ms-1" title="Mettre à jour le stock">
                                                <i class="bi bi-save"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Aucune donnée de stock trouvée.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3 flex-wrap gap-2">
                    <span class="text-muted small">Affichage des produits par niveau de stock</span>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link text-muted" href="#">Précédent</a></li>
                        <li class="page-item active"><a class="page-link text-white" style="background-color: var(--vert); border-color: var(--vert);" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" style="color: var(--vert);" href="#">Suivant</a></li>
                    </ul>
                </div>
                
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>