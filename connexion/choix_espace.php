<?php
session_start();

// Security Check: If they aren't logged in, or if they are just a normal client, kick them out
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'livreur')) {
    header("location: ../acceuille/acceuille.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choix de l'espace - SoinVital</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }
        h2 { color: #2d5a3f; } /* Matching your SoinVital green */
        p { color: #555; margin-bottom: 30px; }
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            text-decoration: none;
            color: white;
            background-color: #2d5a3f;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
            box-sizing: border-box;
        }
        .btn:hover { background-color: #1e3f2b; }
        .btn-public {
            background-color: #e0e0e0;
            color: #333;
        }
        .btn-public:hover { background-color: #cccccc; }
    </style>
</head>
<body>

    <div class="container">
        <!-- Greets them by their first name -->
        <h2>Bonjour, <?= htmlspecialchars($_SESSION['user_prenom']) ?> !</h2>
        <p>Veuillez choisir l'espace auquel vous souhaitez accéder :</p>

        <!-- Dynamic Dashboard Button -->
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="../admin/admin_dashboard.php" class="btn">Gérer le site (Espace Admin)</a>
        <?php elseif ($_SESSION['user_role'] === 'livreur'): ?>
            <a href="../livreur/livreur_dashboard.php" class="btn">Voir mes courses (Espace Livreur)</a>
        <?php endif; ?>

        <!-- Public Website Button -->
        <a href="../acceulle/acceulle.php" class="btn btn-public">Naviguer sur le site public</a>
    </div>

</body>
</html>