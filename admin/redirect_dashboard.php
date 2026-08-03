<?php
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header("location: admin_dashboard.php");
        exit();
    } elseif ($_SESSION['user_role'] === 'livreur') {
        header("location: ../livreur/livreur_dashboard.php"); 
        exit();
    }
}

header("location: ../connexion/connexion.php");
exit();
?>