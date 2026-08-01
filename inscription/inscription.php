<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SoinVital - Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="inscription.css" rel="stylesheet">
</head>
<body>

<div class="inscription-wrapper">
    <div class="inscription-card">

        <div class="inscription-header">
            <div class="mb-2"><span class="logo-text-inscription">Soin<span class="logo-vital-inscription">Vital</span></span></div>
            <h4>Créer un compte</h4>
            <p>Rejoignez la communauté SoinVital</p>
        </div>

        <div class="inscription-body">
            <?php
            if(isset($_SESSION['erreur'])) {
            echo '<div class="message-erreur"><i class="bi bi-exclamation-circle me-2"></i>' . $_SESSION['erreur'] . '</div>';
            unset($_SESSION['erreur']);
                                            }
            ?>
            <form id="inscriptionForm" action="inscription_traitement.php" method="POST" novalidate>

                <p class="section-title-form">Informations personnelles</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="nom" 
                               name="nom" 
                               placeholder="Votre nom"
                               minlength="3"
                               maxlength="30"
                               pattern="[a-zA-ZÀ-ÿ\s]{3,30}"
                               required>
                        <ul class="conditions-list">
                            <li id="nom-longueur"><i class="bi bi-circle-fill"></i> Entre 3 et 30 caractères</li>
                            <li id="nom-lettres"><i class="bi bi-circle-fill"></i> Lettres seulement</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="prenom" 
                               name="prenom" 
                               placeholder="Votre prénom"
                               minlength="3"
                               maxlength="30"
                               pattern="[a-zA-ZÀ-ÿ\s]{3,30}"
                               required>
                        <ul class="conditions-list">
                            <li id="prenom-longueur"><i class="bi bi-circle-fill"></i> Entre 3 et 30 caractères</li>
                            <li id="prenom-lettres"><i class="bi bi-circle-fill"></i> Lettres seulement</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           placeholder="exemple@email.com"
                           pattern="[^\s@]+@[^\s@]+\.[^\s@]+"
                           required>
                    <ul class="conditions-list">
                        <li id="email-format"><i class="bi bi-circle-fill"></i> Format email valide (exemple@email.com)</li>
                    </ul>
                </div>

                <div class="mt-3">
                    <label class="form-label">Téléphone <span class="text-danger">*</span></label>
                    <input type="tel" 
                           class="form-control" 
                           id="telephone" 
                           name="telephone" 
                           placeholder="06XXXXXXXX ou +212XXXXXXXXX"
                           pattern="(\+212|0)[5-7][0-9]{8}"
                           minlength="10"
                           maxlength="15"
                           required>
                    <ul class="conditions-list">
                        <li id="tel-format"><i class="bi bi-circle-fill"></i> Format valide (06XXXXXXXX ou +212XXXXXXXXX)</li>
                        <li id="tel-longueur"><i class="bi bi-circle-fill"></i> Entre 10 et 15 chiffres</li>
                    </ul>
                </div>

                <div class="mt-3">
                    <label class="form-label">Mot de passe <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" 
                               class="form-control" 
                               id="mot_de_passe" 
                               name="mot_de_passe" 
                               placeholder="Votre mot de passe"
                               pattern="(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*]).{8,}"
                               minlength="8"
                               required>
                        <button type="button" class="btn-toggle" id="toggleMdp">
                            <i class="bi bi-eye" id="iconMdp"></i>
                        </button>
                    </div>
                    <ul class="conditions-list">
                        <li id="mdp-longueur"><i class="bi bi-circle-fill"></i> Minimum 8 caractères</li>
                        <li id="mdp-majuscule"><i class="bi bi-circle-fill"></i> Au moins une majuscule</li>
                        <li id="mdp-chiffre"><i class="bi bi-circle-fill"></i> Au moins un chiffre</li>
                        <li id="mdp-special"><i class="bi bi-circle-fill"></i> Au moins un caractère spécial (!@#$%)</li>
                    </ul>
                </div>

                <div class="mt-3">
                    <label class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="password" 
                               class="form-control" 
                               id="confirmer_mdp" 
                               name="confirmer_mdp" 
                               placeholder="Confirmez votre mot de passe"
                               minlength="8"
                               required>
                        <button type="button" class="btn-toggle" id="toggleConfirm">
                            <i class="bi bi-eye" id="iconConfirm"></i>
                        </button>
                    </div>
                    <ul class="conditions-list">
                        <li id="mdp-match"><i class="bi bi-circle-fill"></i> Les mots de passe correspondent</li>
                    </ul>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn-inscrire">
                        <i class="bi bi-person-plus me-2"></i>Créer mon compte
                    </button>
                </div>

                <div class="divider mt-4">
                    <span>ou</span>
                </div>

                <div class="lien-connexion">
                    Déjà un compte ? <a href="../connexion/connexion.php">Connectez-vous</a>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="inscription.js"></script>
</body>
</html>