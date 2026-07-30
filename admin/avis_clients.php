<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

// Fetch reviews matching your actual database table structure
$query = "SELECT 
            a.id_avis, 
            a.note, 
            a.commentaire, 
            a.date_avis, 
            p.nom AS produit_nom, 
            p.image AS produit_image,
            u.nom AS client_nom, 
            u.prenom AS client_prenom
          FROM avis a
          JOIN produits p ON a.id_produit = p.id_produit
          JOIN clients c ON a.id_client = c.id_client
          JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur
          ORDER BY a.date_avis DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$avis_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function for date formatting
function formatReviewDate($dateString) {
    $timestamp = strtotime($dateString);
    return date('d/m/Y', $timestamp) . ' à ' . date('H:i', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Avis Clients</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="admin.css" rel="stylesheet">
</head>
<body>

    <!-- SIDEBAR NAVIGATION -->
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
            <a href="promotions.php" class="sidebar-link">
                <i class="bi bi-percent"></i> Promotions
            </a>
            <a href="avis_clients.php" class="sidebar-link active">
                <i class="bi bi-star"></i> Avis Clients
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="admin-main">
        <!-- Top Header -->
        <header class="admin-header">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Retours & Modération</h5>
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

        <!-- Dashboard Content -->
        <div class="admin-content">
            
            <div class="admin-table-card">
                <!-- Header Actions -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Modération des Avis</h5>
                </div>

                <!-- Table -->
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                            <tr>
                                <th style="width: 30%;">Produit & Client</th>
                                <th style="width: 20%;">Note</th>
                                <th style="width: 35%;">Commentaire</th>
                                <th class="text-end" style="width: 15%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($avis_list) > 0): ?>
                                <?php foreach($avis_list as $avis): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center mb-2">
                                            <img src="<?php echo htmlspecialchars($avis['produit_image']); ?>" alt="Produit" class="rounded me-2" style="width: 35px; height: 35px; object-fit: cover;">
                                            <span class="text-dark fw-bold small text-truncate" style="max-width: 150px;"><?php echo htmlspecialchars($avis['produit_nom']); ?></span>
                                        </div>
                                        <div class="text-muted small"><i class="bi bi-person me-1"></i><?php echo htmlspecialchars($avis['client_prenom'] . ' ' . $avis['client_nom']); ?></div>
                                    </td>
                                    <td>
                                        <div class="text-warning">
                                            <?php 
                                                // Generate Stars
                                                $note = (int)$avis['note'];
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $note) {
                                                        echo '<i class="bi bi-star-fill"></i>';
                                                    } else {
                                                        echo '<i class="bi bi-star"></i>';
                                                    }
                                                }
                                            ?>
                                        </div>
                                        <div class="text-muted small mt-1"><?php echo formatReviewDate($avis['date_avis']); ?></div>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-dark small" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            "<?php echo htmlspecialchars($avis['commentaire']); ?>"
                                        </p>
                                    </td>
                                    <td class="text-end">
                                        <a href="traiter_avis.php?id=<?php echo $avis['id_avis']; ?>&action=supprimer" class="btn btn-sm btn-danger shadow-sm" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet avis ?');">
                                            <i class="bi bi-trash"></i> Supprimer
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Aucun avis trouvé.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3 flex-wrap gap-2">
                    <span class="text-muted small">Affichage de 1 à <?php echo count($avis_list); ?> avis</span>
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item disabled"><a class="page-link text-muted" href="#">Précédent</a></li>
                        <li class="page-item active"><a class="page-link text-white" style="background-color: var(--vert); border-color: var(--vert);" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link text-muted" href="#">Suivant</a></li>
                    </ul>
                </div>
                
            </div>
        </div>
    </main>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>