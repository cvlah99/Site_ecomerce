<?php
session_start();
require_once 'check_auth.php';
require_once '../config/connexion_db.php';

$id_avis = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;

if ($id_avis && $action) {
    try {
        if ($action === 'approuver') {
            $stmt = $pdo->prepare("UPDATE avis SET statut = 'publie' WHERE id_avis = ?");
            $stmt->execute([$id_avis]);
        } elseif ($action === 'rejeter') {
            $stmt = $pdo->prepare("UPDATE avis SET statut = 'rejete' WHERE id_avis = ?");
            $stmt->execute([$id_avis]);
        } elseif ($action === 'supprimer') {
            $stmt = $pdo->prepare("DELETE FROM avis WHERE id_avis = ?");
            $stmt->execute([$id_avis]);
        }
        
        header("Location: avis_clients.php?success=1");
        exit();
    } catch (PDOException $e) {
        header("Location: avis_clients.php?erreur=1");
        exit();
    }
}

header("Location: avis_clients.php");
exit();
?>