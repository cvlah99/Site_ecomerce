<?php
session_start();
// Connect to the 
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

// Fetch clients and aggregate their order data (Total orders and Total spent)
$query = "SELECT 
            c.id_client, 
            u.nom, 
            u.prenom, 
            u.email, 
            u.telephone, 
            u.statut, 
            u.date_inscription,
            COUNT(co.id_commande) as nb_commandes,
            COALESCE(SUM(co.montant_total), 0) as total_depense
          FROM clients c
          INNER JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur
          LEFT JOIN commandes co ON c.id_client = co.id_client
          GROUP BY 
            c.id_client, 
            u.nom, 
            u.prenom, 
            u.email, 
            u.telephone, 
            u.statut, 
            u.date_inscription
          ORDER BY u.date_inscription DESC";
$stmt = $pdo->prepare($query);
$stmt->execute();
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to translate months to French
function formatFrenchDate($dateString) {
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
    <title>SoinVital - Clients Admin</title>
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
            <a href="clients.php" class="sidebar-link active">
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
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Gestion des Clients</h5>
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
                    <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Répertoire Clients</h5>
                    <button class="btn btn-outline-secondary fw-bold px-4" style="border-radius: 8px;">
                        <i class="bi bi-download me-2"></i>Exporter CSV
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                            <tr>
                                <th>Client</th>
                                <th>Contact</th>
                                <th>Date d'inscription</th>
                                <th>Commandes</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($clients) > 0): ?>
                                <?php foreach($clients as $client): ?>
                                    <?php 
                                        // Dynamic Avatar Letter and Color Logic
                                        $initial = strtoupper(substr($client['prenom'], 0, 1));
                                        $colors = ['bg-success', 'bg-primary', 'bg-info', 'bg-warning', 'bg-danger'];
                                        $colorIndex = strlen($client['nom']) % count($colors);
                                        $avatarColor = $colors[$colorIndex];
                                        
                                        // Status Logic
                                        $isActif = strtolower($client['statut']) === 'actif';
                                        $badgeClass = $isActif ? "bg-success bg-opacity-10 text-success" : "bg-secondary bg-opacity-10 text-secondary";
                                        $statusText = $isActif ? "Actif" : "Inactif";
                                    ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle <?php echo $avatarColor; ?> text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 45px; height: 45px;">
                                                <?php echo $initial; ?>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></h6>
                                                <small class="text-muted">ID: CLI-<?php echo str_pad($client['id_client'], 4, '0', STR_PAD_LEFT); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark small"><i class="bi bi-envelope me-1 text-muted"></i> <?php echo htmlspecialchars($client['email']); ?></div>
                                        <div class="text-dark small mt-1"><i class="bi bi-telephone me-1 text-muted"></i> <?php echo htmlspecialchars($client['telephone']); ?></div>
                                    </td>
                                    <td>
                                        <div class="text-dark"><?php echo formatFrenchDate($client['date_inscription']); ?></div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark"><?php echo $client['nb_commandes']; ?></span>
                                        <small class="text-muted ms-1">(Total: <?php echo number_format($client['total_depense'], 2); ?> MAD)</small>
                                    </td>
                                    <td><span class="badge <?php echo $badgeClass; ?> px-3 py-2 rounded-pill"><?php echo $statusText; ?></span></td>
                                    <td class="text-end">
                                        <!-- Optional: View Profile / Details -->
    <a href="modifier_client.php?id=<?php echo $client['id_client']; ?>" class="btn btn-sm btn-light text-primary me-1 shadow-sm" title="Modifier / Voir profil">
        <i class="bi bi-person-lines-fill"></i>
    </a>
    
    <!-- Delete / Deactivate client via ID -->
    <a href="supprimer_client.php?id=<?php echo $client['id_client']; ?>" class="btn btn-sm btn-light text-danger shadow-sm" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client et son compte associé ?');">
        <i class="bi bi-trash"></i>
    </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Aucun client trouvé.</td>
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