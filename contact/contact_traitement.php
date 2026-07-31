<?php
session_start();
require_once '../config/connexion_db.php';

if(isset($_POST["nom"], $_POST["email"], $_POST["sujet"], $_POST["message"])){
    $nom = trim(htmlspecialchars($_POST["nom"]));
    $email = trim(htmlspecialchars($_POST["email"]));
    $sujet = trim(htmlspecialchars($_POST["sujet"]));
    $message = trim(htmlspecialchars($_POST["message"]));

    if(empty($nom) || strlen($nom) < 3 || strlen($nom) > 30){
        $_SESSION['erreur'] = "Le nom est invalide !";
        header("location:contact.php");
        exit();
    }

    if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['erreur'] = "L'adresse email n'est pas valide !";
        header("location:contact.php");
        exit();
    }

    if(empty($sujet) || strlen($sujet) < 5 || strlen($sujet) > 200){
        $_SESSION['erreur'] = "Le sujet est invalide !";
        header("location:contact.php");
        exit();
    }

    if(empty($message) || strlen($message) < 20){
        $_SESSION['erreur'] = "Le message doit contenir au moins 20 caractères !";
        header("location:contact.php");
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO messages_contact (nom, email, sujet, message) 
                            VALUES (:nom, :email, :sujet, :message)");
    $stmt->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':sujet' => $sujet,
        ':message' => $message
    ]);

    $_SESSION['succes'] = "Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.";
    header("location:contact.php");
    exit();

} else {
    header("location:contact.php");
    exit();
}
?>