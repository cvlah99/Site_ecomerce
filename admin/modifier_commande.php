<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

$erreur = '';
$succes = '';
$id_commande = $_GET['id'] ?? null;

if (!$id_commande) {
    header("Location: commandes.php");
    exit();
}

// Handle status update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_statut = $_POST['statut'] ?? '';
    
    if (!empty($nouveau_statut)) {
        try {
            $updateQuery = "UPDATE commandes SET statut = ? WHERE id_commande = ?";
            $pdo->prepare($updateQuery)->execute([$nouveau_statut, $id_commande]);
            $succes = "Le statut de la commande a été mis à jour avec succès.";
        } catch (PDOException $e) {
            $erreur = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

// Fetch order details along with client info
$queryOrder = "SELECT co.*, u.nom, u.prenom, u.email, u.telephone, cl.adresse, cl.ville 
               FROM commandes co
               JOIN clients cl ON co.id_client = cl.id_client
               JOIN utilisateurs u ON cl.id_utilisateur = u.id_utilisateur
               WHERE co.id_commande = ?";
$stmtO = $pdo->prepare($queryOrder);
$stmtO->execute([$id_commande]);
$commande = $stmtO->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    header("Location: commandes.php");
    exit();
}

// Fetch products/items linked to this order
$queryItems = "SELECT cp.quantite, cp.prix_unitaire, p.nom, p.image 
               FROM commande_produits cp
               JOIN produits p ON cp.id_produit = p.id_produit
               WHERE cp.id_commande = ?";
$stmtI = $pdo->prepare($queryItems);
$stmtI->execute([$id_commande]);
$articles = $stmtI->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Détails Commande #<?php echo $id_commande; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-content p-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="admin-table-card p-4 bg-white shadow-sm rounded-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">
                            Commande #CMD-<?php echo str_pad($commande['id_commande'], 4, '0', STR_PAD_LEFT); ?>
                        </h5>
                        <a href="commandes.php" class="btn btn-light shadow-sm"><i class="bi bi-arrow-left me-2"></i>Retour aux commandes</a>
                    </div>

                    <?php if(!empty($erreur)): ?>
                        <div class="alert alert-danger"><?php echo $erreur; ?></div>
                    <?php endif; ?>
                    <?php if(!empty($succes)): ?>
                        <div class="alert alert-success"><?php echo $succes; ?></div>
                    <?php endif; ?>

                    <div class="row g-4 mb-4">
                        <!-- Client Info Card -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-bold text-dark text-uppercase small mb-3"><i class="bi bi-person-fill me-2"></i>Informations Client</h6>
                                <p class="mb-1 fw-bold"><?php echo htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']); ?></p>
                                <p class="mb-1 text-muted small"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($commande['email']); ?></p>
                                <p class="mb-1 text-muted small"><i class="bi bi-telephone me-2"></i><?php echo htmlspecialchars($commande['telephone']); ?></p>
                                <p class="mb-0 text-muted small"><i class="bi bi-geo-alt me-2"></i><?php echo htmlspecialchars($commande['adresse'] . ', ' . $commande['ville']); ?></p>
                            </div>
                        </div>

                        <!-- Order Status & Update Card -->
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <h6 class="fw-bold text-dark text-uppercase small mb-3"><i class="bi bi-gear-fill me-2"></i>Gestion du Statut</h6>
                                <form method="POST" action="">
                                    <label class="form-label small text-muted fw-bold">Modifier le statut de la commande</label>
                                    <div class="input-group mb-3">
                                        <select name="statut" class="form-select">
                                            <option value="en_attente" <?php echo ($commande['statut'] == 'en_attente') ? 'selected' : ''; ?>>En attente</option>
                                            <option value="en_cours" <?php echo ($commande['statut'] == 'en_cours') ? 'selected' : ''; ?>>En cours</option>
                                            <option value="livre" <?php echo ($commande['statut'] == 'livre') ? 'selected' : ''; ?>>Livré</option>
                                            <option value="annule" <?php echo ($commande['statut'] == 'annule') ? 'selected' : ''; ?>>Annulé</option>
                                        </select>
                                        <button type="submit" class="btn text-white fw-bold px-3" style="background: var(--vert); border: none;">Mettre à jour</button>
                                    </div>
                                    <small class="text-muted">Date de commande : <?php echo $commande['date_commande']; ?></small>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Ordered Items Table -->
                    <h6 class="fw-bold text-dark text-uppercase small mb-3"><i class="bi bi-box-seam me-2"></i>Articles Commandés</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light text-muted small text-uppercase">
                                <tr>
                                    <th>Produit</th>
                                    <th>Prix Unitaire</th>
                                    <th>Quantité</th>
                                    <th class="text-end">Total Ligne</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($articles) > 0): ?>
                                    <?php foreach($articles as $article): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                                    <span class="fw-bold text-dark"><?php echo htmlspecialchars($article['nom']); ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo number_format($article['prix_unitaire'], 2); ?> MAD</td>
                                            <td><?php echo $article['quantite']; ?></td>
                                            <td class="text-end fw-bold"><?php echo number_format($article['prix_unitaire'] * $article['quantite'], 2); ?> MAD</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">Aucun article trouvé pour cette commande.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold text-uppercase">Montant Total :</td>
                                    <td class="text-end fw-bold text-success fs-5"><?php echo number_format($commande['montant_total'], 2); ?> MAD</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>