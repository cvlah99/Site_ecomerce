<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_livreur = $_GET['id'];

    try {
        // First find the associated id_utilisateur so we can delete from the parent table
        $stmt_get = $pdo->prepare("SELECT id_utilisateur FROM livreurs WHERE id_livreur = ?");
        $stmt_get->execute([$id_livreur]);
        $livreur = $stmt_get->fetch(PDO::FETCH_ASSOC);

        if ($livreur) {
            $id_utilisateur = $livreur['id_utilisateur'];

            // Deleting from utilisateurs will cascade and delete the livreur record automatically
            $stmt_del = $pdo->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
            $stmt_del->execute([$id_utilisateur]);
        }

        header("Location: livreurs.php?success=supprime");
        exit();
    } catch (PDOException $e) {
        header("Location: livreurs.php?erreur=suppression_impossible");
        exit();
    }
} else {
    header("Location: livreurs.php");
    exit();
}
?>