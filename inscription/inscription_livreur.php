<?php
session_start();
require_once '../config/connexion_db.php'; // Adjust path if necessary

$erreur = '';
$succes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $vehicule = trim($_POST['vehicule'] ?? '');

    if (!empty($nom) && !empty($prenom) && !empty($email) && !empty($telephone) && !empty($mot_de_passe)) {
        try {
            // Check if email already exists
            $stmtCheck = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ?");
            $stmtCheck->execute([$email]);
            
            if ($stmtCheck->rowCount() > 0) {
                $erreur = "Cet e-mail est déjà utilisé.";
            } else {
                // 1. Insert into utilisateurs table with 'livreur' role
                $hashed_password = password_hash($mot_de_passe, PASSWORD_DEFAULT);
                $stmtUtilisateur = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role) VALUES (?, ?, ?, ?, ?, 'livreur')");
                $stmtUtilisateur->execute([$nom, $prenom, $email, $telephone, $hashed_password]);
                
                $id_utilisateur = $pdo->lastInsertId();

                // 2. Insert into livreurs table (defaulting to 'disponible' status)
                $stmtLivreur = $pdo->prepare("INSERT INTO livreurs (id_utilisateur, vehicule, statut) VALUES (?, ?, 'disponible')");
                $stmtLivreur->execute([$id_utilisateur, $vehicule]);

                $succes = "Votre inscription en tant que livreur a réussi ! Vous pouvez maintenant vous connecter.";
            }
        } catch (PDOException $e) {
            $erreur = "Erreur lors de l'inscription : " . $e->getMessage();
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
    <title>SoinVital - Inscription Livreur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        }
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .register-header {
            background-color: var(--vert);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .logo-text {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 2rem;
        }
        .logo-vital {
            color: var(--or);
        }
        .btn-custom {
            background-color: var(--or);
            color: white;
            border: none;
            font-weight: bold;
            padding: 12px;
        }
        .btn-custom:hover {
            background-color: #b39770;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="register-card">
                    <div class="register-header">
                        <div class="logo-text mb-2">Soin<span class="logo-vital">Vital</span></div>
                        <h5 class="mb-0" style="font-weight: 300;">Rejoignez notre flotte de livraison</h5>
                    </div>
                    
                    <div class="p-4 p-md-5">
                        <?php if(!empty($erreur)): ?>
                            <div class="alert alert-danger"><?php echo $erreur; ?></div>
                        <?php endif; ?>
                        
                        <?php if(!empty($succes)): ?>
                            <div class="alert alert-success">
                                <?php echo $succes; ?>
                                <br><br>
                                <a href="login.php" class="btn btn-outline-success btn-sm">Aller à la connexion</a>
                            </div>
                        <?php else: ?>
                        
                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Prénom *</label>
                                    <input type="text" name="prenom" class="form-control bg-light" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Nom *</label>
                                    <input type="text" name="nom" class="form-control bg-light" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-uppercase">Adresse E-mail *</label>
                                    <input type="email" name="email" class="form-control bg-light" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Téléphone *</label>
                                    <input type="tel" name="telephone" class="form-control bg-light" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold small text-uppercase">Type de Véhicule</label>
                                    <select name="vehicule" class="form-select bg-light">
                                        <option value="Moto">Moto</option>
                                        <option value="Voiture">Voiture</option>
                                        <option value="Camionnette">Camionnette</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold small text-uppercase">Mot de passe *</label>
                                    <input type="password" name="mot_de_passe" class="form-control bg-light" required>
                                </div>
                                
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-custom w-100 rounded-3">S'inscrire en tant que Livreur</button>
                                </div>
                            </div>
                        </form>
                        
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>