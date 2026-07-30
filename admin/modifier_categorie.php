<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

$erreur = '';
$id_categorie = $_GET['id'] ?? null;

if (!$id_categorie) {
    header("Location: categories.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM categories WHERE id_categorie = ?");
$stmt->execute([$id_categorie]);
$categorie = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$categorie) {
    header("Location: categories.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $description = $_POST['description'] ?? '';
    $ordre_affichage = $_POST['ordre_affichage'] ?? 0;

    if (!empty($nom)) {
        try {
            $query = "UPDATE categories SET nom = ?, description = ?, ordre_affichage = ? WHERE id_categorie = ?";
            $pdo->prepare($query)->execute([$nom, $description, $ordre_affichage, $id_categorie]);

            header("Location: categories.php?success=modifie");
            exit();
        } catch (PDOException $e) {
            $erreur = "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    } else {
        $erreur = "Le nom de la catégorie est obligatoire.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Modifier une Catégorie</title>
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
                        <h5 class="mb-0 fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Modifier la Catégorie</h5>
                        <a href="categories.php" class="btn btn-light shadow-sm"><i class="bi bi-arrow-left me-2"></i>Retour</a>
                    </div>
                    
                    <?php if(!empty($erreur)): ?>
                        <div class="alert alert-danger"><?php echo $erreur; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase">Nom de la catégorie *</label>
                                <input type="text" name="nom" class="form-control bg-light" required value="<?php echo htmlspecialchars($categorie['nom']); ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold small text-uppercase">Description</label>
                                <textarea name="description" class="form-control bg-light" rows="3"><?php echo htmlspecialchars($categorie['description']); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Ordre d'affichage</label>
                                <input type="number" name="ordre_affichage" class="form-control bg-light" value="<?php echo htmlspecialchars($categorie['ordre_affichage']); ?>">
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