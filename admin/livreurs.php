<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

// Fetch all delivery drivers ordered by their current status
$query = "SELECT 
            l.id_livreur,
            u.nom,
            u.prenom,
            u.telephone,
            u.email,
            u.statut,
            l.vehicule,
            l.zone_livraison,
            l.disponibilite,
            l.note_moyenne
          FROM livreurs l
          INNER JOIN utilisateurs u ON l.id_utilisateur = u.id_utilisateur
          ORDER BY 
            FIELD(u.statut, 'disponible', 'en_livraison', 'inactif'), 
            u.nom ASC";

$stmt = $pdo->prepare($query);
$stmt->execute();
$livreurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Flotte Livreurs</title>
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
            <a href="gestion_stock.php" class="sidebar-link">
                <i class="bi bi-boxes"></i> Gestion du Stock
            </a>

            <div class="menu-label">Utilisateurs & Logistique</div>
            <a href="clients.php" class="sidebar-link">
                <i class="bi bi-people"></i> Clients
            </a>
            <a href="livreurs.php" class="sidebar-link active">
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
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Logistique & Livraison</h5>
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
                    <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Gestion de la Flotte</h5>
                    
                    <!-- NEW: Updated 'Nouveau Livreur' button with <a> tag -->
                    <a href="ajouter_livreur.php" class="text-decoration-none">
                        <button class="btn text-white fw-bold px-4" style="background: var(--vert); border: none; border-radius: 8px;">
                            <i class="bi bi-plus-lg me-2"></i>Nouveau Livreur
                        </button>
                    </a>
                </div>

                <div class="row mb-4 g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 bg-light" placeholder="Rechercher par nom...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select bg-light">
                            <option value="">Tous les statuts</option>
                            <option value="disponible">Disponible</option>
                            <option value="en_livraison">En livraison</option>
                            <option value="inactif">Inactif / Repos</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select bg-light">
                            <option value="">Toutes les zones</option>
                            <option value="centre">Oujda - Centre</option>
                            <option value="lazaret">Oujda - Lazaret</option>
                            <option value="village">Oujda - Village Touba</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex justify-content-end">
                         <button class="btn btn-light w-100"><i class="bi bi-funnel"></i> Filtrer</button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="text-muted" style="font-size: 0.85rem; text-transform: uppercase;">
                            <tr>
                                <th>Livreur</th>
                                <th>Contact & Véhicule</th>
                                <th>Zone Assignée</th>
                                <th>Commandes en cours</th>
                                <th>Statut Actuel</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($livreurs) > 0): ?>
                                <?php foreach($livreurs as $livreur): ?>
                                    <?php 
                                        // Status badge logic
                                        switch($livreur['statut']) {
                                            case 'disponible':
                                                $badge = 'bg-success bg-opacity-10 text-success';
                                                $icon = 'bi-check-circle-fill';
                                                $texte = 'Disponible';
                                                $cmdCount = 0;
                                                break;
                                            case 'en_livraison':
                                                $badge = 'bg-info bg-opacity-10 text-info';
                                                $icon = 'bi-bicycle';
                                                $texte = 'En livraison';
                                                $cmdCount = rand(2, 5); // Mocking active orders for the prototype UI
                                                break;
                                            case 'inactif':
                                            default:
                                                $badge = 'bg-secondary bg-opacity-10 text-secondary';
                                                $icon = 'bi-pause-circle-fill';
                                                $texte = 'Inactif';
                                                $cmdCount = 0;
                                                break;
                                        }

                                        // Vehicle Icon Logic
                                        $vehiculeIcon = (strtolower($livreur['vehicule']) == 'moto') ? 'bi-scooter' : 'bi-truck';
                                    ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light text-dark d-flex align-items-center justify-content-center border me-3" style="width: 45px; height: 45px;">
                                                <i class="bi bi-person-badge fs-5 text-muted"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.95rem;"><?php echo htmlspecialchars($livreur['prenom'] . ' ' . $livreur['nom']); ?></h6>
                                                <small class="text-muted">ID: LIV-<?php echo str_pad($livreur['id_livreur'], 3, '0', STR_PAD_LEFT); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-dark small fw-bold mb-1"><i class="bi bi-telephone-fill me-2 text-muted"></i><?php echo htmlspecialchars($livreur['telephone']); ?></div>
                                        <span class="badge bg-light text-dark border px-2 py-1"><i class="bi <?php echo $vehiculeIcon; ?> me-1"></i> <?php echo htmlspecialchars($livreur['vehicule']); ?></span>
                                    </td>
                                    <td><span class="text-dark"><i class="bi bi-geo-alt text-danger me-1"></i> <?php echo htmlspecialchars($livreur['zone_livraison']); ?></span></td>
                                    <td>
                                        <?php if($cmdCount > 0): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                                <?php echo $cmdCount; ?> Colis
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">Aucune</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge <?php echo $badge; ?> px-3 py-2 rounded-pill"><i class="bi <?php echo $icon; ?> me-1"></i><?php echo $texte; ?></span></td>
                                    
                                    <!-- NEW: Updated action buttons with dynamic <a> tags -->
                                    <td class="text-end">
                                        <!-- Lien vers modifier_livreur.php -->
                                        <a href="modifier_livreur.php?id=<?php echo $livreur['id_livreur']; ?>" class="btn btn-sm btn-light text-primary me-1 shadow-sm" title="Modifier profil">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        
                                        <!-- Lien vers supprimer_livreur.php avec confirmation -->
                                        <a href="supprimer_livreur.php?id=<?php echo $livreur['id_livreur']; ?>" class="btn btn-sm btn-light text-danger shadow-sm" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livreur ? Cette action supprimera également son compte utilisateur.');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>

                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Aucun livreur enregistré dans la base de données.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mt-4 border-top pt-3 flex-wrap gap-2">
                    <span class="text-muted small">Affichage de 1 à <?php echo count($livreurs); ?> sur <?php echo count($livreurs); ?> livreurs</span>
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