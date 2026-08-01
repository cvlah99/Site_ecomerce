<?php
session_start();
require_once '../config/connexion_db.php';

// Check if an order ID was passed in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: livreur_dashboard.php');
    exit;
}

$id_commande = (int)$_GET['id'];
$id_livreur = $_SESSION['id_livreur'] ?? 1; // Simulate driver session

// 1. Fetch Order & Client Details
$query_commande = "SELECT 
            c.id_commande, 
            c.date_commande, 
            c.montant_total, 
            c.statut, 
            c.adresse_livraison,
            u.nom AS client_nom, 
            u.prenom AS client_prenom, 
            u.telephone AS client_telephone,
            u.email AS client_email
          FROM commandes c
          JOIN clients cl ON c.id_client = cl.id_client
          JOIN utilisateurs u ON cl.id_utilisateur = u.id_utilisateur
          WHERE c.id_commande = ? AND c.id_livreur = ?";

$stmt = $pdo->prepare($query_commande);
$stmt->execute([$id_commande, $id_livreur]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    die("Commande introuvable ou vous n'avez pas l'autorisation de la voir.");
}

// 2. Fetch Products inside this Order
// Note: Adjust the table name 'lignes_commande' if your database uses a different name for order details (e.g., 'details_commande')
$query_produits = "SELECT 
                    p.nom, 
                    p.image, 
                    lc.quantite, 
                    lc.prix_unitaire 
                   FROM commande_produits lc
                   JOIN produits p ON lc.id_produit = p.id_produit
                   WHERE lc.id_commande = ?";
$stmt_prod = $pdo->prepare($query_produits);
$stmt_prod->execute([$id_commande]);
$produits = $stmt_prod->fetchAll(PDO::FETCH_ASSOC);

// Format WhatsApp number
$whatsappNumber = preg_replace('/^0/', '212', preg_replace('/[^0-9]/', '', $commande['client_telephone']));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Commande - SoinVital</title>
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
            <a href="livreur_dashboard.php" class="sidebar-link">
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
                <a href="livreur_dashboard.php" class="btn btn-sm btn-light border me-3 shadow-sm">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <h5 class="mb-0 text-dark fw-bold" style="font-family: 'Lato', sans-serif;">Détails de la Commande</h5>
            </div>
        </header>

        <div class="admin-content">
            <div class="row g-4">
                <!-- Client & Delivery Info -->
                <div class="col-lg-4">
                    <div class="admin-table-card h-100">
                        <h5 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif;">Informations Client</h5>
                        
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold">Nom du Client</label>
                            <p class="fw-bold text-dark fs-5 mb-0"><?php echo htmlspecialchars($commande['client_prenom'] . ' ' . $commande['client_nom']); ?></p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="text-muted small text-uppercase fw-bold">Contact</label>
                            <div>
                                <a href="tel:<?php echo htmlspecialchars($commande['client_telephone']); ?>" class="text-decoration-none text-dark d-block mb-1">
                                    <i class="bi bi-telephone-fill text-success me-2"></i><?php echo htmlspecialchars($commande['client_telephone']); ?>
                                </a>
                                <a href="https://wa.me/<?php echo $whatsappNumber; ?>" target="_blank" class="btn btn-sm btn-success w-100 mt-2">
                                    <i class="bi bi-whatsapp me-1"></i> Envoyer un message
                                </a>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-0">
                            <label class="text-muted small text-uppercase fw-bold">Adresse de Livraison</label>
                            <p class="text-dark mb-0"><i class="bi bi-geo-alt-fill text-danger me-2"></i><?php echo nl2br(htmlspecialchars($commande['adresse_livraison'])); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Products List -->
                <div class="col-lg-8">
                    <div class="admin-table-card h-100">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold mb-0" style="font-family: 'Playfair Display', serif;">Contenu du Colis (Réf: CMD-<?php echo str_pad($commande['id_commande'], 4, '0', STR_PAD_LEFT); ?>)</h5>
                            <span class="badge bg-light text-dark border px-3 py-2 fs-6">Total à encaisser: <span style="color: var(--vert);"><?php echo number_format($commande['montant_total'], 2); ?> MAD</span></span>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="text-muted small text-uppercase">
                                    <tr>
                                        <th>Produit</th>
                                        <th class="text-center">Quantité</th>
                                        <th class="text-end">Prix Unitaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($produits) > 0): ?>
                                        <?php foreach($produits as $prod): ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo htmlspecialchars($prod['image']); ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                        <span class="fw-bold text-dark"><?php echo htmlspecialchars($prod['nom']); ?></span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-light text-dark border px-2 py-1 fs-6">x<?php echo $prod['quantite']; ?></span>
                                                </td>
                                                <td class="text-end fw-bold text-dark">
                                                    <?php echo number_format($prod['prix_unitaire'], 2); ?> MAD
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">Aucun produit trouvé pour cette commande.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>