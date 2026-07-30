<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

$erreur = '';

// Fetch products for the dropdown selection
$produits = $pdo->query("SELECT id_produit, nom FROM produits ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code_promo = $_POST['code_promo'] ?? '';
    $id_produit = $_POST['id_produit'] ?? '';
    $pourcentage_remise = $_POST['pourcentage_remise'] ?? 0;
    $date_debut = $_POST['date_debut'] ?? '';
    $date_fin = $_POST['date_fin'] ?? '';
    $description = $_POST['description'] ?? '';
    $statut = $_POST['statut'] ?? 'actif';

    if (!empty($code_promo) && !empty($id_produit) && !empty($pourcentage_remise)) {
        try {
            $query = "INSERT INTO promotions (code_promo, id_produit, pourcentage_remise, date_debut, date_fin, description, statut) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$code_promo, $id_produit, $pourcentage_remise, $date_debut, $date_fin, $description, $statut]);

            header("Location: promotions.php?success=ajoute");
            exit();
        } catch (PDOException $e) {
            $erreur = "Erreur lors de l'enregistrement : " . $e->getMessage();
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
    <title>SoinVital - Créer une Promotion</title>
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
                        <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Créer un Code Promo</h5>
                        <a href="promotions.php" class="btn btn-light shadow-sm"><i class="bi bi-arrow-left me-2"></i>Retour</a>
                    </div>
                    
                    <?php if(!empty($erreur)): ?>
                        <div class="alert alert-danger"><?php echo $erreur; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Code Promo *</label>
                                <input type="text" name="code_promo" class="form-control bg-light" required placeholder="Ex: ETE2026">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Produit concerné *</label>
                                <select name="id_produit" class="form-select bg-light" required>
                                    <option value="">Sélectionner un produit...</option>
                                    <?php foreach($produits as $p): ?>
                                        <option value="<?php echo $p['id_produit']; ?>"><?php echo htmlspecialchars($p['nom']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Pourcentage de Remise (%) *</label>
                                <input type="number" step="0.01" name="pourcentage_remise" class="form-control bg-light" required placeholder="Ex: 20">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Statut *</label>
                                <select name="statut" class="form-select bg-light">
                                    <option value="actif">Actif</option>
                                    <option value="a_venir">À venir</option>
                                    <option value="expire">Expiré</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Date de début *</label>
                                <input type="date" name="date_debut" class="form-control bg-light" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Date de fin *</label>
                                <input type="date" name="date_fin" class="form-control bg-light" required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase">Description</label>
                                <textarea name="description" class="form-control bg-light" rows="3" placeholder="Détails de l'offre promotionnelle..."></textarea>
                            </div>
                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn text-white fw-bold px-4" style="background: var(--or); border: none;">Enregistrer la promotion</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>