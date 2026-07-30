<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_produit = $_GET['id'];

    try {
        // Because stocks, avis, and promotions have ON DELETE CASCADE, 
        // deleting the product will automatically clean up those tables!
        $query = "DELETE FROM produits WHERE id_produit = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_produit]);
        
        // Redirect back with a success parameter (optional, for your UI)
        header("Location: produits.php?success=supprime");
        exit();
        
    } catch (PDOException $e) {
        // If the product is linked to a past order (ON DELETE RESTRICT), this block catches the error
        // You can capture this error parameter in produits.php to show a red alert box
        header("Location: produits.php?erreur=impossible_lie_commande");
        exit();
    }
} else {
    header("Location: produits.php");
    exit();
}
?>