<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

// Fetch orders and join with clients to get their names
$query = "SELECT 
            co.id_commande, 
            co.date_commande, 
            co.montant_total, 
            co.statut, 
            u.nom, 
            u.prenom
          FROM commandes co
          JOIN clients cl ON co.id_client = cl.id_client
          JOIN utilisateurs u ON cl.id_utilisateur = u.id_utilisateur
          ORDER BY co.date_commande DESC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function for date and time formatting
function formatOrderDate($dateString) {
    $timestamp = strtotime($dateString);
    $date = date('d M Y', $timestamp);
    $time = date('H:i', $timestamp);
    
    // Quick check for "Today" or "Yesterday"
    $today = date('d M Y');
    $yesterday = date('d M Y', strtotime('-1 days'));
    
    if ($date === $today) {
        $date = "Aujourd'hui";
    } elseif ($date === $yesterday) {
        $date = "Hier";
    }
    
    return ['date' => $date, 'time' => $time];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Commandes Admin</title>
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
            <a href="commandes.php" class="sidebar-link active">
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
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Gestion des Commandes</h5>
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
                    <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Historique des Commandes</h5>
                    <button class="btn btn-outline-secondary fw-bold px-4" style="border-radius: 8px;">
                        <i class="bi bi-download me-2"></i>Exporter CSV
                    </button>
                </div>

                <div class="row mb-4 g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 bg-light" placeholder="Rechercher par ID ou Client...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select bg-light">
                            <option value="">Tous les statuts</option>
                            <option value="attente">En attente</option>
                            <option value="cours">En cours</option>
                            <option value="livre">Livré</option>
                            <option value="annule">Annulé</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control bg-light" title="Filtrer par date">
                    </div>
                    <div class="col-md-2 d-flex justify-content-end">
                         <button class="btn btn-light w-100"><i class="bi bi-funnel"></i> Filtrer</button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                            <tr>
                                <th>ID Commande</th>
                                <th>Client</th>
                                <th>Date & Heure</th>
                                <th>Articles</th>
                                <th>Total (MAD)</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($commandes) > 0): ?>
                                <?php foreach($commandes as $cmd): ?>
                                    <?php 
                                        $formattedDate = formatOrderDate($cmd['date_commande']);
                                        
                                        // Status badge logic
                                switch($cmd['statut']) {
                                    case 'en attente':
                                    case 'en_attente':
                                        $badge = 'bg-warning bg-opacity-10 text-warning';
                                        $icon = 'bi-clock';
                                        $texte = 'En attente';
                                        break;
                                    case 'en_cours':
                                        $badge = 'bg-info bg-opacity-10 text-info';
                                        $icon = 'bi-box-seam';
                                        $texte = 'En cours';
                                        break;
                                    case 'livre':
                                        $badge = 'bg-success bg-opacity-10 text-success';
                                        $icon = 'bi-check2-all';
                                        $texte = 'Livré';
                                        break;
                                    case 'annule':
                                        $badge = 'bg-danger bg-opacity-10 text-danger';
                                        $icon = 'bi-x-circle';
                                        $texte = 'Annulé';
                                        break;
                                    default:
                                        $badge = 'bg-secondary bg-opacity-10 text-secondary';
                                        $icon = 'bi-question-circle';
                                        $texte = $cmd['statut'];
                                        break;
                                }
                                    ?>
                                <tr>
                                    <td><span class="fw-bold text-dark">#CMD-<?php echo str_pad($cmd['id_commande'], 4, '0', STR_PAD_LEFT); ?></span></td>
                                    <td>
                                        <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;"><?php echo htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']); ?></h6>
                                    </td>
                                    <td>
                                        <div class="text-dark"><?php echo $formattedDate['date']; ?></div>
                                        <small class="text-muted"><?php echo $formattedDate['time']; ?></small>
                                    </td>
                                    <td><span class="text-muted small">Voir détails</span></td>
                                    <td class="fw-bold text-dark"><?php echo number_format($cmd['montant_total'], 2); ?></td>
                                    <td><span class="badge <?php echo $badge; ?> px-3 py-2 rounded-pill"><i class="bi <?php echo $icon; ?> me-1"></i><?php echo $texte; ?></span></td>
                                    <td class="text-end">
                                        <a href="modifier_commande.php?id=<?php echo $cmd['id_commande']; ?>" class="btn btn-sm btn-light text-primary shadow-sm" title="Voir / Modifier les détails">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Aucune commande trouvée.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3 flex-wrap gap-2">
                    <span class="text-muted small">Affichage de 1 à <?php echo count($commandes); ?> commandes</span>
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