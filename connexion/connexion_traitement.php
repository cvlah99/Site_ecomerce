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
    } elseif(strlen($mot_de_passe) < 8){
        $_SESSION['erreur'] = "Le mot de passe doit contenir au moins 8 caractères !";
        header("location:connexion.php");
        exit();
    }
    $stmt = $pdo->prepare("SELECT u.*, c.id_client FROM utilisateurs u 
                            LEFT JOIN clients c ON u.id_utilisateur = c.id_utilisateur
                            WHERE u.email = :email");
    $stmt->execute([':email' => $email]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$utilisateur){
        $_SESSION['erreur'] = "Email ou mot de passe incorrect !";
        header("location:connexion.php");
        exit();
    }
    if(!password_verify($mot_de_passe, $utilisateur['mot_de_passe'])){
        $_SESSION['erreur'] = "Email ou mot de passe incorrect !";
        header("location:connexion.php");
        exit();
    }

    if($utilisateur['statut'] !== 'actif'){
        $_SESSION['erreur'] = "Votre compte est désactivé. Contactez le support !";
        header("location:connexion.php");
        exit();
    }
    $_SESSION['user_id'] = $utilisateur['id_utilisateur'];
    $_SESSION['user_nom'] = $utilisateur['nom'];
    $_SESSION['user_prenom'] = $utilisateur['prenom'];
    $_SESSION['user_email'] = $utilisateur['email'];
    $_SESSION['user_role'] = 'client';
    $_SESSION['id_client'] = $utilisateur['id_client'];

    header("location:../acceulle/acceulle.php");
    exit();

} else {
    header("location:connexion.php");
    exit();
}
?>