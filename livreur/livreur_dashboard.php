<?php
session_start();
require_once '../config/connexion_db.php';

// Simulate driver session
$id_livreur = $_SESSION['id_livreur'] ?? 1; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_commande'], $_POST['nouveau_statut'])) {
    $id_commande = $_POST['id_commande'];
    $nouveau_statut = $_POST['nouveau_statut'];

    try {
        $updateQuery = "UPDATE commandes SET statut = ? WHERE id_commande = ?";
        $stmtUpdate = $pdo->prepare($updateQuery);
        $stmtUpdate->execute([$nouveau_statut, $id_commande]);
        header("Location: livreur_dashboard.php?success=statut_mis_a_jour");
        exit();
    } catch (PDOException $e) {
        $erreur = "Erreur lors de la mise à jour.";
    }
}

$query = "SELECT 
            c.id_commande, 
            c.date_commande, 
            c.montant_total, 
            c.statut, 
            c.adresse_livraison,
            u.nom AS client_nom, 
            u.prenom AS client_prenom, 
            u.telephone AS client_telephone
          FROM commandes c
          JOIN clients cl ON c.id_client = cl.id_client
          JOIN utilisateurs u ON cl.id_utilisateur = u.id_utilisateur
          WHERE c.id_livreur = ?
          ORDER BY FIELD(c.statut, 'en_cours', 'en_attente', 'livre'), c.date_commande DESC";

$stmt = $pdo->prepare($query);
$stmt->execute([$id_livreur]);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Espace Livreur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="../admin/admin.css" rel="stylesheet">
</head>
<body>
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <a href="livreur_dashboard.php" class="text-decoration-none">
                <span class="logo-text">Soin<span class="logo-vital">Vital</span></span>
            </a>
        </div>
        <div class="sidebar-menu">
            <div class="menu-label">Espace Livreur</div>
            <a href="livreur_dashboard.php" class="sidebar-link active">
                <i class="bi bi-truck"></i> Mes Livraisons
            </a>
            <div class="menu-label">Session</div>
            <a href="../acceulle/acceulle.php" class="sidebar-link text-danger">
                <i class="bi bi-box-arrow-right"></i> Déconnexion
            </a>
        </div>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Tableau de Bord Livreur</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2 border-start ps-3 ms-2">
                    <div class="text-end d-none d-md-block">
                        <p class="mb-0 fw-bold text-dark" style="font-size: 0.9rem;">Livreur</p>
                        <p class="mb-0 text-muted" style="font-size: 0.8rem;">En Service</p>
                    </div>
                    <div class="rounded-circle text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-weight: bold; background-color: var(--vert);">
                        L
                    </div>
                </div>
            </div>
        </header>

        <div class="admin-content">
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Statut mis à jour avec succès !
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="admin-table-card">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Commandes Attribuées</h5>
                    <span class="badge bg-secondary px-3 py-2"><?php echo count($commandes); ?> total</span>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                            <tr>
                                <th>Réf / Date</th>
                                <th>Client & Contact</th>
                                <th>Adresse de Livraison</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th class="text-end">Actions & Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($commandes) > 0): ?>
                                <?php foreach($commandes as $cmd): ?>
                                    <?php 
                                        switch($cmd['statut']) {
                                            case 'livré':
                                                $badgeClass = 'bg-success bg-opacity-10 text-success';
                                                $statusText = 'Livré';
                                                break;
                                            case 'en_cours':
                                                $badgeClass = 'bg-primary bg-opacity-10 text-primary';
                                                $statusText = 'En cours';
                                                break;
                                            default:
                                                $badgeClass = 'bg-warning bg-opacity-10 text-warning';
                                                $statusText = 'En attente';
                                                break;
                                        }
                                        
                                        // Format phone number for WhatsApp (remove leading 0 and add country code if needed, assuming Moroccan +212)
                                        $whatsappNumber = preg_replace('/^0/', '212', preg_replace('/[^0-9]/', '', $cmd['client_telephone']));
                                    ?>
                                <tr>
                                    <td>
                                        <h6 class="mb-0 fw-bold text-dark">CMD-<?php echo str_pad($cmd['id_commande'], 4, '0', STR_PAD_LEFT); ?></h6>
                                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($cmd['date_commande'])); ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($cmd['client_prenom'] . ' ' . $cmd['client_nom']); ?></div>
                                        <!-- Quick Contact Action Buttons -->
                                        <div class="d-flex gap-1">
                                            <a href="tel:<?php echo htmlspecialchars($cmd['client_telephone']); ?>" class="btn btn-sm btn-light border text-success shadow-sm" title="Appeler">
                                                <i class="bi bi-telephone-fill"></i>
                                            </a>
                                            <a href="https://wa.me/<?php echo $whatsappNumber; ?>" target="_blank" class="btn btn-sm btn-light border text-success shadow-sm" title="WhatsApp">
                                                <i class="bi bi-whatsapp"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="small text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo htmlspecialchars($cmd['adresse_livraison']); ?></span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark"><?php echo number_format($cmd['montant_total'], 2); ?> MAD</span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $badgeClass; ?> px-3 py-2 rounded-pill"><?php echo $statusText; ?></span>
                                    </td>
                                    <td class="text-end">
                                        <a href="details_commande.php?id=<?php echo $cmd['id_commande']; ?>" class="btn btn-sm btn-light border text-primary shadow-sm me-1" title="Voir les détails">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                        <form method="POST" action="" class="d-inline-flex align-items-center gap-1 justify-content-end">
                                            <input type="hidden" name="id_commande" value="<?php echo $cmd['id_commande']; ?>">
                                            <select name="nouveau_statut" class="form-select form-select-sm bg-light" style="width: 130px;">
                                                <option value="en_attente" <?php echo ($cmd['statut'] == 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                                                <option value="en_cours" <?php echo ($cmd['statut'] == 'en_cours') ? 'selected' : ''; ?>>En cours</option>
                                                <option value="livré" <?php echo ($cmd['statut'] == 'livré') ? 'selected' : ''; ?>>Livré</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm text-white shadow-sm" style="background-color: var(--vert);" title="Mettre à jour">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Aucune commande assignée pour le moment.</td>
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