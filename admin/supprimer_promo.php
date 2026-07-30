<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_promo = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM promotions WHERE id_promo = ?");
        $stmt->execute([$id_promo]);

        header("Location: promotions.php?success=supprime");
        exit();
    } catch (PDOException $e) {
        header("Location: promotions.php?erreur=suppression_impossible");
        exit();
    }
} else {
    header("Location: promotions.php");
    exit();
}
?>