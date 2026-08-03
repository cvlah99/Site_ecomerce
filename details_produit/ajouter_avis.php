<?php
session_start();
require_once '../config/connexion_db.php';

// Vérifier si la requête est bien un POST et si l'utilisateur est un client
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_client'])) {
    
    $id_client = $_SESSION['id_client'];
    $id_produit = intval($_POST['id_produit']);
    $note = intval($_POST['note']);
    $commentaire = trim($_POST['commentaire']);
    
    // Vérification de sécurité basique
    if ($note >= 1 && $note <= 5 && !empty($commentaire) && $id_produit > 0) {
       try {
            // Insertion dans la base de données. 
            $stmt = $pdo->prepare("INSERT INTO avis (id_produit, id_client, note, commentaire, date_avis, statut) VALUES (?, ?, ?, ?, CURDATE(), 'en_attente')");
            $stmt->execute([$id_produit, $id_client, $note, $commentaire]);
            
            // Redirection silencieuse vers la page du produit
            header("Location: details_produit.php?id=" . $id_produit);
            exit();
            
        } catch (PDOException $e) {
            // En cas d'erreur, on redirige silencieusement aussi
            header("Location: details_produit.php?id=" . $id_produit);
            exit();
        }
    }
}

// Sécurité : redirection si on accède à cette page directement
header("Location: ../catalogue/catalogue.php");
exit();
?>