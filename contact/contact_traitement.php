<?php
session_start();
require_once '../config/connexion_db.php';

// Check if the form was actually submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Capture and clean the inputs
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $sujet = trim($_POST['sujet'] ?? '');
    $message = trim($_POST['message'] ?? '');

    // 2. Validate that fields aren't empty (as a backend backup to your JS)
    if (empty($nom) || empty($email) || empty($sujet) || empty($message)) {
        $_SESSION['erreur'] = "Veuillez remplir tous les champs obligatoires.";
        header("Location: contact.php");
        exit();
    }

    // 3. Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erreur'] = "Le format de l'adresse email est invalide.";
        header("Location: contact.php");
        exit();
    }

    try {
        // 4. Insert the message into the database
        $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, email, sujet, message, statut) VALUES (?, ?, ?, ?, 'nouveau')");
        $stmt->execute([$nom, $email, $sujet, $message]);

        // 5. Send a success message back to the user
        $_SESSION['succes'] = "Votre message a été envoyé avec succès. Notre équipe vous répondra dans les plus brefs délais !";
        header("Location: contact.php");
        exit();

    } catch (PDOException $e) {
        // If there is a database error, catch it so it doesn't crash the page
        $_SESSION['erreur'] = "Une erreur s'est produite lors de l'envoi de votre message. Veuillez réessayer plus tard.";
        header("Location: contact.php");
        exit();
    }

} else {
    // If someone tries to access this file directly without submitting the form, kick them back
    header("Location: contact.php");
    exit();
}
?>