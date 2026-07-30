<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_categorie = $_GET['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM categories WHERE id_categorie = ?");
        $stmt->execute([$id_categorie]);

        header("Location: categories.php?success=supprime");
        exit();
    } catch (PDOException $e) {
        // Handled if products are still linked and restricted
        header("Location: categories.php?erreur=suppression_impossible");
        exit();
    }
} else {
    header("Location: categories.php");
    exit();
}
?>