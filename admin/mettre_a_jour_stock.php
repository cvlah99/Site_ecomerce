<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_produit = $_POST['id_produit'] ?? null;
    $quantite = $_POST['quantite_disponible'] ?? null;

    if ($id_produit !== null && $quantite !== null && is_numeric($quantite)) {
        try {
            $query = "UPDATE stocks SET quantite_disponible = ?, date_mise_a_jour = CURDATE() WHERE id_produit = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$quantite, $id_produit]);

            header("Location: gestion_stock.php?success=stock_mis_a_jour");
            exit();
        } catch (PDOException $e) {
            header("Location: gestion_stock.php?erreur=sql_error");
            exit();
        }
    }
}

header("Location: gestion_stock.php");
exit();
?>