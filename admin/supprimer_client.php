<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_client = $_GET['id'];

    try {
        // Find the associated id_utilisateur first
        $stmt_get = $pdo->prepare("SELECT id_utilisateur FROM clients WHERE id_client = ?");
        $stmt_get->execute([$id_client]);
        $client = $stmt_get->fetch(PDO::FETCH_ASSOC);

        if ($client) {
            $id_utilisateur = $client['id_utilisateur'];

            // Deleting from utilisateurs will cascade and delete the client profile automatically
            $stmt_del = $pdo->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = ?");
            $stmt_del->execute([$id_utilisateur]);
        }

        header("Location: clients.php?success=supprime");
        exit();
    } catch (PDOException $e) {
        // Restricted if client has past orders (ON DELETE RESTRICT on commandes)
        header("Location: clients.php?erreur=suppression_impossible");
        exit();
    }
} else {
    header("Location: clients.php");
    exit();
}
?>