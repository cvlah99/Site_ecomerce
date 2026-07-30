<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

$erreur = '';
$id_livreur = $_GET['id'] ?? null;

if (!$id_livreur) {
    header("Location: livreurs.php");
    exit();
}

// Fetch existing driver and user details
$query = "SELECT l.*, u.nom, u.prenom, u.email, u.telephone, u.statut 
          FROM livreurs l 
          JOIN utilisateurs u ON l.id_utilisateur = u.id_utilisateur 
          WHERE l.id_livreur = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id_livreur]);
$livreur = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$livreur) {
    header("Location: livreurs.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $vehicule = $_POST['vehicule'] ?? '';
    $zone_livraison = $_POST['zone_livraison'] ?? '';
    $statut = $_POST['statut'] ?? 'disponible';

    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($vehicule)) {
        try {
            // 1. Update core user details
            $updateUser = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ?, statut = ? WHERE id_utilisateur = ?";
            $pdo->prepare($updateUser)->execute([$nom, $prenom, $email, $telephone, $statut, $livreur['id_utilisateur']]);

            // 2. Update driver-specific details
            $updateLivreur = "UPDATE livreurs SET vehicule = ?, zone_livraison = ? WHERE id_livreur = ?";
            $pdo->prepare($updateLivreur)->execute([$vehicule, $zone_livraison, $id_livreur]);

            header("Location: livreurs.php?success=modifie");
            exit();
        } catch (PDOException $e) {
            $erreur = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    } else {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Modifier un Livreur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-content p-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="admin-table-card p-4 bg-white shadow-sm rounded-4">
                    <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Modifier le Livreur</h5>
                        <a href="livreurs.php" class="btn btn-light shadow-sm"><i class="bi bi-arrow-left me-2"></i>Retour</a>
                    </div>
                    
                    <?php if(!empty($erreur)): ?>
                        <div class="alert alert-danger"><?php echo $erreur; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Prénom</label>
                                <input type="text" name="prenom" class="form-control bg-light" required value="<?php echo htmlspecialchars($livreur['prenom']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Nom</label>
                                <input type="text" name="nom" class="form-control bg-light" required value="<?php echo htmlspecialchars($livreur['nom']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Email</label>
                                <input type="email" name="email" class="form-control bg-light" required value="<?php echo htmlspecialchars($livreur['email']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Téléphone</label>
                                <input type="text" name="telephone" class="form-control bg-light" value="<?php echo htmlspecialchars($livreur['telephone']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Véhicule</label>
                                <input type="text" name="vehicule" class="form-control bg-light" required value="<?php echo htmlspecialchars($livreur['vehicule']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Zone de Livraison</label>
                                <input type="text" name="zone_livraison" class="form-control bg-light" required value="<?php echo htmlspecialchars($livreur['zone_livraison']); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase">Statut</label>
                                <select name="statut" class="form-select bg-light">
                                    <option value="disponible" <?php echo ($livreur['statut'] == 'disponible') ? 'selected' : ''; ?>>Disponible</option>
                                    <option value="en_livraison" <?php echo ($livreur['statut'] == 'en_livraison') ? 'selected' : ''; ?>>En livraison</option>
                                    <option value="inactif" <?php echo ($livreur['statut'] == 'inactif') ? 'selected' : ''; ?>>Inactif</option>
                                </select>
                            </div>
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn text-white fw-bold px-4" style="background: var(--vert); border: none;">Mettre à jour</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>