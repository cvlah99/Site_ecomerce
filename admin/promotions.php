<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

// Fetch all promotions matching your actual database columns
$query = "SELECT 
            pr.id_promo, 
            pr.pourcentage_remise, 
            pr.date_debut, 
            pr.date_fin, 
            pr.statut, 
            p.nom AS nom_produit, 
            p.image AS image_produit,
            p.prix AS prix_original,
            (p.prix - (p.prix * (pr.pourcentage_remise / 100))) AS prix_remise
          FROM promotions pr
          JOIN produits p ON pr.id_produit = p.id_produit
          ORDER BY pr.date_fin ASC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$promotions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to translate dates to French
function formatFrenchDateOnly($dateString) {
    $months = ["Jan", "Fév", "Mar", "Avr", "Mai", "Juin", "Juil", "Août", "Sep", "Oct", "Nov", "Déc"];
    $timestamp = strtotime($dateString);
    $day = date('d', $timestamp);
    $monthIndex = date('n', $timestamp) - 1;
    $year = date('Y', $timestamp);
    return $day . " " . $months[$monthIndex] . " " . $year;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Promotions</title>
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
            <a href="livreurs.php" class="sidebar-link">
                <i class="bi bi-truck"></i> Livreurs
            </a>

            <div class="menu-label">Marketing</div>
            <a href="promotions.php" class="sidebar-link active">
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
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Marketing & Réductions</h5>
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
                    <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Gestion des Promotions</h5>
                    
                    <a href="ajouter_promo.php" class="text-decoration-none">
                        <button class="btn text-white fw-bold px-4" style="background: var(--or); border: none; border-radius: 8px;">
                            <i class="bi bi-plus-lg me-2"></i>Créer une Promo
                        </button>
                    </a>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                            <tr>
                                <th>Produit</th>
                                <th>Remise</th>
                                <th>Période de Validité</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($promotions) > 0): ?>
                                <?php foreach($promotions as $promo): ?>
                                    <?php 
                                        $remise = rtrim(rtrim($promo['pourcentage_remise'], '0'), '.');
                                        
                                        // Status logic
                                        switch($promo['statut']) {
                                            case 'actif':
                                                $badgeClass = 'bg-success bg-opacity-10 text-success';
                                                $statusText = 'Actif';
                                                $icon = 'bi-check-circle-fill';
                                                break;
                                            case 'a_venir':
                                                $badgeClass = 'bg-warning bg-opacity-10 text-warning';
                                                $statusText = 'À venir';
                                                $icon = 'bi-clock-fill';
                                                break;
                                            case 'expire':
                                            default:
                                                $badgeClass = 'bg-danger bg-opacity-10 text-danger';
                                                $statusText = 'Expiré';
                                                $icon = 'bi-x-circle-fill';
                                                break;
                                        }
                                    ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo htmlspecialchars($promo['produit_image']); ?>" alt="" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($promo['nom_produit']); ?></h6>
                                                <small class="text-muted">Ancien: <?php echo number_format($promo['prix_original'], 2); ?> MAD &rarr; <span class="text-success fw-bold">Nouveau: <?php echo number_format($promo['prix_remise'], 2); ?> MAD</span></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary bg-opacity-10 text-primary border px-3 py-2 fs-6 fw-bold">
                                            -<?php echo $remise; ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-dark small"><i class="bi bi-calendar-event me-1 text-muted"></i> Du <?php echo formatFrenchDateOnly($promo['date_debut']); ?></div>
                                        <div class="text-dark small mt-1"><i class="bi bi-calendar-x me-1 text-muted"></i> Au <?php echo formatFrenchDateOnly($promo['date_fin']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $badgeClass; ?> px-3 py-2 rounded-pill">
                                            <i class="bi <?php echo $icon; ?> me-1"></i><?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="modifier_promo.php?id=<?php echo $promo['id_promo']; ?>" class="btn btn-sm btn-light text-primary me-1 shadow-sm" title="Modifier">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <a href="supprimer_promo.php?id=<?php echo $promo['id_promo']; ?>" class="btn btn-sm btn-light text-danger shadow-sm" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Aucune promotion trouvée.</td>
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