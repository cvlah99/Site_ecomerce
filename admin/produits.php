<?php
session_start();
// Connect to the database
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

// Fetch all products, their categories, and their current stock
$query = "SELECT 
            p.id_produit, 
            p.nom, 
            p.description, 
            p.prix, 
            p.image, 
            p.date_ajout, 
            p.marque, 
            c.nom as nom_categorie, 
            s.quantite_disponible 
          FROM produits p 
          JOIN categories c ON p.id_categorie = c.id_categorie 
          LEFT JOIN stocks s ON p.id_produit = s.id_produit
          ORDER BY p.date_ajout DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Produits Admin</title>
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
            <a href="avis_clients.php" class="sidebar-link">
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
                    <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Catalogue Produits</h5>
                    
                    <a href="ajouter_produit.php" class="text-decoration-none">
                        <button class="btn text-white fw-bold px-4 shadow-sm" style="background: var(--or); border: none; border-radius: 8px;">
        <i class="bi bi-plus-lg me-2"></i>Nouveau Produit
    </button>
                    </a>
                </div>

                <div class="row mb-4 g-3">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 bg-light" placeholder="Rechercher par produit ou référence...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select bg-light">
                            <option value="">Toutes les catégories</option>
                            <option value="1">Compléments alimentaires</option>
                            <option value="2">Cosmétiques</option>
                            <option value="3">Huiles Essentielles</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex justify-content-end">
                         <button class="btn btn-light w-100"><i class="bi bi-funnel"></i> Filtrer</button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                            <tr>
                                <th>Produit</th>
                                <th>Catégorie</th>
                                <th>Prix (MAD)</th>
                                <th>Stock</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($produits) > 0): ?>
                                <?php foreach($produits as $produit): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo htmlspecialchars($produit['image']); ?>" alt="Image produit" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover; margin-right: 15px;">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($produit['nom']); ?></h6>
                                                <small class="text-muted">Réf: PROD-<?php echo str_pad($produit['id_produit'], 4, '0', STR_PAD_LEFT); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="text-muted"><?php echo htmlspecialchars($produit['nom_categorie']); ?></span></td>
                                    <td class="fw-bold text-dark"><?php echo number_format($produit['prix'], 2); ?></td>
                                    
                                    <?php 
                                    $stock = $produit['quantite_disponible'] ?? 0;
                                    if ($stock > 10) {
                                        $stockClass = "text-dark";
                                        $badgeClass = "bg-success bg-opacity-10 text-success";
                                        $statusText = "Actif";
                                    } elseif ($stock > 0) {
                                        $stockClass = "text-warning fw-bold";
                                        $badgeClass = "bg-warning bg-opacity-10 text-warning";
                                        $statusText = "Stock Faible";
                                    } else {
                                        $stockClass = "text-danger fw-bold";
                                        $badgeClass = "bg-danger bg-opacity-10 text-danger";
                                        $statusText = "Rupture";
                                    }
                                    ?>
                                    
                                    <td class="<?php echo $stockClass; ?>"><?php echo $stock; ?></td>
                                    <td><span class="badge <?php echo $badgeClass; ?> px-3 py-2 rounded-pill"><?php echo $statusText; ?></span></td>
                                    <td class="text-end">
                                        <!-- Lien vers modifier_produit.php avec l'ID du produit -->
    <a href="modifier_produit.php?id=<?php echo $produit['id_produit']; ?>" class="btn btn-sm btn-light text-primary me-1 shadow-sm" title="Modifier">
        <i class="bi bi-pencil-square"></i>
    </a>
    
    <!-- Lien vers supprimer_produit.php avec l'ID du produit et une alerte de confirmation -->
    <a href="supprimer_produit.php?id=<?php echo $produit['id_produit']; ?>" class="btn btn-sm btn-light text-danger shadow-sm" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ? Cette action est irréversible.');">
        <i class="bi bi-trash"></i>
    </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Aucun produit trouvé dans la base de données.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3 flex-wrap gap-2">
                    <span class="text-muted small">Affichage de 1 à <?php echo count($produits); ?> sur <?php echo count($produits); ?> produits</span>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link text-muted" href="#">Précédent</a></li>
                        <li class="page-item active"><a class="page-link text-white" style="background-color: var(--vert); border-color: var(--vert);" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link text-muted" href="#">Suivant</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>