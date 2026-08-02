<?php
session_start();
// Connect to the database
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

// 1. Get Total Products
$stmt_prod = $pdo->query("SELECT COUNT(*) FROM produits");
$total_produits = $stmt_prod->fetchColumn();

// 2. Get Total Categories

$stmt_cat = $pdo->query("SELECT COUNT(*) FROM categories");
$total_categories = $stmt_cat->fetchColumn();

// 3. Get Low Stock Alerts (Products with 5 or fewer items left)
$stmt_stock = $pdo->query("SELECT COUNT(*) FROM stocks WHERE quantite_disponible <= 5");
$alertes_stock = $stmt_stock->fetchColumn();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Tableau de Bord</title>
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
            <a href="admin_dashboard.php" class="sidebar-link active">
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
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Aperçu Général</h5>
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
            
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-muted mb-0 fw-bold">Total Produits</h6>
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-box-seam fs-5"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-1"><?php echo $total_produits; ?></h2>
                            <small class="text-success fw-bold"><i class="bi bi-arrow-up-short"></i> Catalogue en ligne</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-muted mb-0 fw-bold">Catégories Actives</h6>
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-tags fs-5"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-1"><?php echo $total_categories; ?></h2>
                            <small class="text-muted">Familles de produits</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-muted mb-0 fw-bold">Alertes Stock</h6>
                                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-exclamation-triangle fs-5"></i>
                                </div>
                            </div>
                            <h2 class="fw-bold text-dark mb-1"><?php echo $alertes_stock; ?></h2>
                            <small class="text-danger fw-bold">Articles nécessitant un réapprovisionnement</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info border-0 shadow-sm rounded-4" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>
                <strong>Note:</strong> Les statistiques de commandes et de revenus seront affichées ici une fois que les tables correspondantes seront créées dans la base de données.
            </div>

        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>