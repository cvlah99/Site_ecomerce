<?php
session_start();
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
    <title>SoinVital - Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="connexion.css" rel="stylesheet">
</head>
<body>

<div class="connexion-wrapper">
    <div class="connexion-card">

        <div class="connexion-header">
            <span class="logo-text-connexion">Soin<span class="logo-vital-connexion">Vital</span></span>
            <h4>Bon retour !</h4>
            <p>Connectez-vous à votre compte</p>
        </div>

        <div class="connexion-body">

            <?php if(isset($erreur)): ?>
            <div class="message-erreur">
                <i class="bi bi-exclamation-circle me-2"></i><?php echo $erreur; ?>
            </div>
            <?php endif; ?>

            <form id="connexionForm" action="connexion_traitement.php" method="POST" novalidate>
                <div class="mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email"
                           class="form-control"
                           id="email"
                           name="email"
                           placeholder="exemple@email.com"
                           pattern="[^\s@]+@[^\s@]+\.[^\s@]+"
                           required>
                    <ul class="conditions-list">
                        <li id="email-format"><i class="bi bi-circle-fill"></i> Identifiant requis</li>
                    </ul>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password"
                               class="form-control"
                               id="mot_de_passe"
                               name="mot_de_passe"
                               placeholder="Votre mot de passe"
                               required>
                        <button type="button" class="btn-toggle" id="toggleMdp">
                            <i class="bi bi-eye" id="iconMdp"></i>
                        </button>
                    </div>
                    <ul class="conditions-list">
                        <li id="mdp-longueur"><i class="bi bi-circle-fill"></i> Mot de passe requis</li>
                    </ul>
                </div>
                <div class="mt-4">
                    <button type="submit" class="btn-connecter">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                    </button>
                </div>

                <div class="divider mt-4">
                    <span>ou</span>
                </div>

                <div class="lien-inscription">
                    Pas encore de compte ? <a href="../inscription/inscription.php">S'inscrire</a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="connexion.js"></script>
</body>
</html>