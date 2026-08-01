<?php
session_start();
require_once '../config/connexion_db.php';

if(isset($_POST["email"], $_POST["mot_de_passe"])){
    $email = trim(htmlspecialchars($_POST["email"]));
    $mot_de_passe = trim($_POST["mot_de_passe"]);
    
    if(empty($email)){
        $_SESSION['erreur'] = "L'email est obligatoire !";
        header("location:connexion.php");
        exit();
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['erreur'] = "L'adresse email n'est pas valide !";
        header("location:connexion.php");
        exit();
    }
    
    if(empty($mot_de_passe)){
        $_SESSION['erreur'] = "Le mot de passe est obligatoire !";
        header("location:connexion.php");
        exit();
    } /*elseif(strlen($mot_de_passe) < 8){
        $_SESSION['erreur'] = "Le mot de passe doit contenir au moins 8 caractères !";
        header("location:connexion.php");
        exit();
    }*/
    
    // UPGRADE: Added a LEFT JOIN for the livreurs table to grab the id_livreur if they are a driver
    $stmt = $pdo->prepare("SELECT u.*, c.id_client, l.id_livreur 
                           FROM utilisateurs u 
                           LEFT JOIN clients c ON u.id_utilisateur = c.id_utilisateur
                           LEFT JOIN livreurs l ON u.id_utilisateur = l.id_utilisateur
                           WHERE u.email = :email");
    $stmt->execute([':email' => $email]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$utilisateur){
        $_SESSION['erreur'] = "Email ou mot de passe incorrect !";
        header("location:connexion.php");
        exit();
    }
    
 
// Check if it's a valid hash, OR if it's an admin/livreur using a plain text password
    $is_valid_hash = password_verify($mot_de_passe, $utilisateur['mot_de_passe']);
    $is_plain_text_staff = (($utilisateur['role'] === 'admin' || $utilisateur['role'] === 'livreur') && $mot_de_passe === $utilisateur['mot_de_passe']);

    // If BOTH checks fail, the password is wrong! Kick them out.
    if(!$is_valid_hash && !$is_plain_text_staff){
        $_SESSION['erreur'] = "Email ou mot de passe incorrect !";
        header("location:connexion.php");
        exit();
    }

    if($utilisateur['statut'] !== 'actif'){
        $_SESSION['erreur'] = "Votre compte est désactivé. Contactez le support !";
        header("location:connexion.php");
        exit();
    }
    
    // Set standard session variables for everyone
    $_SESSION['user_id'] = $utilisateur['id_utilisateur'];
    $_SESSION['user_nom'] = $utilisateur['nom'];
    $_SESSION['user_prenom'] = $utilisateur['prenom'];
    $_SESSION['user_email'] = $utilisateur['email'];
    
    // UPGRADE: Dynamically set the role based on the database instead of hardcoding 'client'
    $_SESSION['user_role'] = $utilisateur['role']; 

    // UPGRADE: The Smart Router
    // UPGRADE: The Gateway Router
    if ($utilisateur['role'] === 'livreur') {
        $_SESSION['id_livreur'] = $utilisateur['id_livreur'];
        header("location: choix_espace.php"); // <-- Send to Gateway
        exit();
    } elseif ($utilisateur['role'] === 'admin') {
        header("location: choix_espace.php"); // <-- Send to Gateway
        exit();
    } else {
        // Normal clients still go straight to the public site
        $_SESSION['id_client'] = $utilisateur['id_client'];
        header("location: ../acceulle/acceulle.php"); 
        exit();
    }

} else {
    header("location:connexion.php");
    exit();
}
?>