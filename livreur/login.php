<?php
session_start();
// If there's any error message stored from a failed login attempt
$erreur = '';
if(isset($_SESSION['erreur'])) {
    $erreur = $_SESSION['erreur'];
    unset($_SESSION['erreur']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Connexion Livreur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --vert: #2C4A3B;
            --or: #C5A880;
        }
        body {
            background-color: #f8f9fa;
            font-family: 'Lato', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 450px;
            padding: 2.5rem;
        }
        .logo-text {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 2rem;
            color: var(--vert);
            text-align: center;
        }
        .logo-vital {
            color: var(--or);
        }
        .subtitle {
            text-align: center;
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }
        .form-control {
            background-color: #f1f4f2;
            border: 1px solid #dee2e6;
            padding: 0.75rem 0.75rem 0.75rem 2.5rem;
        }
        .input-group-text-custom {
            position: absolute;
            left: 10px;
            top: 50Tpx;
            z-index: 10;
            background: transparent;
            border: none;
            color: #6c757d;
        }
        .input-wrapper {
            position: relative;
        }
        .input-wrapper .bi {
            position: absolute;
            left: 12px;
            top: 38px;
            color: #6c757d;
        }
        .btn-custom {
            background-color: #2C4A3B;
            color: white;
            border: none;
            font-weight: bold;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
        }
        .btn-custom:hover {
            background-color: #1e3328;
            color: white;
        }
        .back-link {
            text-align: center;
            display: block;
            margin-top: 1.5rem;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .back-link:hover {
            color: var(--vert);
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="logo-text mb-1">Soin<span class="logo-vital">Vital</span></div>
        <div class="subtitle">Espace Livreur</div>

        <?php if(!empty($erreur)): ?>
            <div class="alert alert-danger py-2 text-center small"><?php echo $erreur; ?></div>
        <?php endif; ?>

        <!-- Point this action to your main connection handler script -->
        <form action="../connexion/connexion_traitement.php" method="POST">
            
            <div class="mb-3 input-wrapper">
                <label class="form-label fw-bold small text-uppercase text-muted">Email</label>
                <i class="bi bi-envelope"></i>
                <input type="email" name="email" class="form-control rounded-3" placeholder="livreur@soinvital.ma" required>
            </div>

            <div class="mb-4 input-wrapper">
                <label class="form-label fw-bold small text-uppercase text-muted">Mot de passe</label>
                <i class="bi bi-lock"></i>
                <input type="password" name="mot_de_passe" class="form-control rounded-3" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-custom shadow-sm">Se connecter</button>
        </form>

        <a href="../acceulle/acceulle.php" class="back-link">
            <i class="bi bi-arrow-left me-1"></i> Retour au site public
        </a>
    </div>

</body>
</html>