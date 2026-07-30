<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

$erreur = '';
$id_client = $_GET['id'] ?? null;

if (!$id_client) {
    header("Location: clients.php");
    exit();
}

// Fetch client and user info
$query = "SELECT c.*, u.nom, u.prenom, u.email, u.telephone, u.statut 
          FROM clients c 
          JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur 
          WHERE c.id_client = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id_client]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    header("Location: clients.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $email = $_POST['email'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $ville = $_POST['ville'] ?? '';
    $statut = $_POST['statut'] ?? 'actif';

    if (!empty($nom) && !empty($prenom) && !empty($email)) {
        try {
            // Update core user table
            $updateUser = "UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ?, statut = ? WHERE id_utilisateur = ?";
            $pdo->prepare($updateUser)->execute([$nom, $prenom, $email, $telephone, $statut, $client['id_utilisateur']]);

            // Update client specific table
            $updateClient = "UPDATE clients SET adresse = ?, ville = ? WHERE id_client = ?";
            $pdo->prepare($updateClient)->execute([$adresse, $ville, $id_client]);

            header("Location: clients.php?success=modifie");
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
    <title>SoinVital - Modifier un Client</title>
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
                        <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Modifier le Client</h5>
                        <a href="clients.php" class="btn btn-light shadow-sm"><i class="bi bi-arrow-left me-2"></i>Retour</a>
                    </div>
                    
                    <?php if(!empty($erreur)): ?>
                        <div class="alert alert-danger"><?php echo $erreur; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Prénom</label>
                                <input type="text" name="prenom" class="form-control bg-light" required value="<?php echo htmlspecialchars($client['prenom']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Nom</label>
                                <input type="text" name="nom" class="form-control bg-light" required value="<?php echo htmlspecialchars($client['nom']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Email</label>
                                <input type="email" name="email" class="form-control bg-light" required value="<?php echo htmlspecialchars($client['email']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Téléphone</label>
                                <input type="text" name="telephone" class="form-control bg-light" value="<?php echo htmlspecialchars($client['telephone']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Adresse</label>
                                <input type="text" name="adresse" class="form-control bg-light" value="<?php echo htmlspecialchars($client['adresse']); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Ville</label>
                                <input type="text" name="ville" class="form-control bg-light" value="<?php echo htmlspecialchars($client['ville']); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase">Statut du compte</label>
                                <select name="statut" class="form-select bg-light">
                                    <option value="actif" <?php echo ($client['statut'] == 'actif') ? 'selected' : ''; ?>>Actif</option>
                                    <option value="inactif" <?php echo ($client['statut'] == 'inactif') ? 'selected' : ''; ?>>Inactif</option>
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