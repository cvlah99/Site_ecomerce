<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and has admin rights
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['niveau_acces'] !== 'admin' && $_SESSION['niveau_acces'] !== 'super_admin') {
    header("Location: login.php");
    exit();
}
?>