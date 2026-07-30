<?php
session_start();
require_once '../config/connexion_db.php';

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['mot_de_passe'] ?? '');

    if (!empty($email) && !empty($password)) {
        // Query to check if the user exists and is an administrator
        $query = "SELECT u.*, a.niveau_acces 
                  FROM utilisateurs u 
                  JOIN administrateurs a ON u.id_utilisateur = a.id_utilisateur 
                  WHERE u.email = ? AND u.statut = 'actif'";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password (supporting your brother's MD5 setup or standard password hashing)
if ($admin && ($password === $admin['mot_de_passe'] || md5($password) === $admin['mot_de_passe'] || password_verify($password, $admin['mot_de_passe']))) {            // Set session variables
            $_SESSION['id_utilisateur'] = $admin['id_utilisateur'];
            $_SESSION['nom'] = $admin['nom'];
            $_SESSION['prenom'] = $admin['prenom'];
            $_SESSION['niveau_acces'] = $admin['niveau_acces'];

            // Update last login timestamp
            $updateLogin = "UPDATE administrateurs SET derniere_connexion = NOW() WHERE id_utilisateur = ?";
            $pdo->prepare($updateLogin)->execute([$admin['id_utilisateur']]);

            // Redirect to dashboard
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $erreur = "Identifiants incorrects ou accès non autorisé.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Connexion Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="admin.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow-sm p-4 border-0 rounded-4" style="width: 100%; max-width: 400px;">
        <div class="text-center mb-4">
            <span class="logo-text fs-3">Soin<span class="logo-vital">Vital</span></span>
            <p class="text-muted small mt-1">Espace Administration</p>
        </div>

        <?php if(!empty($erreur)): ?>
            <div class="alert alert-danger py-2 small shadow-sm"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control bg-light border-start-0" required placeholder="admin@soinvital.ma">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted text-uppercase">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="mot_de_passe" class="form-control bg-light border-start-0" required placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="btn w-100 text-white fw-bold py-2 shadow-sm" style="background: var(--vert); border: none; border-radius: 8px;">
                Se connecter
            </button>
        </form>
        
        <div class="text-center mt-3">
            <a href="../index.php" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left me-1"></i>Retour au site public</a>
        </div>
    </div>

</body>
</html>