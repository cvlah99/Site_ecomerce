<?php
session_start();
require_once '../config/connexion_db.php';

if(!isset($_SESSION['user_id'])){
    header("location:../connexion/connexion.php");
    exit();
}

if(!isset($_POST['action'])){
    header("location:profil.php");
    exit();
}

$id_utilisateur = $_SESSION['user_id'];
$id_client = $_SESSION['id_client'];
$action = $_POST['action'];

if($action === 'infos'){
    $nom = trim(htmlspecialchars($_POST['nom']));
    $prenom = trim(htmlspecialchars($_POST['prenom']));
    $email = trim(htmlspecialchars($_POST['email']));
    $telephone = trim(htmlspecialchars($_POST['telephone']));

    if(empty($nom) || empty($prenom) || empty($email)){
        $_SESSION['erreur'] = "Veuillez remplir tous les champs obligatoires !";
        header("location:profil.php");
        exit();
    }

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION['erreur'] = "L'adresse email n'est pas valide !";
        header("location:profil.php");
        exit();
    }

    $stmt_check = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = :email AND id_utilisateur != :id");
    $stmt_check->execute([':email' => $email, ':id' => $id_utilisateur]);
    if($stmt_check->rowCount() > 0){
        $_SESSION['erreur'] = "Cet email est déjà utilisé !";
        header("location:profil.php");
        exit();
    }

    $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone WHERE id_utilisateur = :id");
    $stmt->execute([':nom' => $nom, ':prenom' => $prenom, ':email' => $email, ':telephone' => $telephone, ':id' => $id_utilisateur]);

    $_SESSION['user_nom'] = $nom;
    $_SESSION['user_prenom'] = $prenom;
    $_SESSION['user_email'] = $email;

    $_SESSION['succes'] = "Informations mises à jour avec succès !";
    header("location:profil.php");
    exit();
}

if($action === 'adresse'){
    $adresse = trim(htmlspecialchars($_POST['adresse']));
    $ville = trim(htmlspecialchars($_POST['ville']));
    $code_postal = trim(htmlspecialchars($_POST['code_postal']));
    $pays = trim(htmlspecialchars($_POST['pays']));

    $stmt = $pdo->prepare("UPDATE clients SET adresse = :adresse, ville = :ville, code_postal = :code_postal, pays = :pays WHERE id_client = :id_client");
    $stmt->execute([':adresse' => $adresse, ':ville' => $ville, ':code_postal' => $code_postal, ':pays' => $pays, ':id_client' => $id_client]);

    $_SESSION['succes'] = "Adresse mise à jour avec succès !";
    header("location:profil.php");
    exit();
}

if($action === 'mot_de_passe'){
    $ancien_mdp = $_POST['ancien_mdp'];
    $nouveau_mdp = $_POST['nouveau_mdp'];
    $confirmer_mdp = $_POST['confirmer_mdp'];

    $stmt = $pdo->prepare("SELECT mot_de_passe FROM utilisateurs WHERE id_utilisateur = :id");
    $stmt->execute([':id' => $id_utilisateur]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!password_verify($ancien_mdp, $user['mot_de_passe'])){
        $_SESSION['erreur'] = "Le mot de passe actuel est incorrect !";
        header("location:profil.php");
        exit();
    }

    if(strlen($nouveau_mdp) < 8){
        $_SESSION['erreur'] = "Le nouveau mot de passe doit contenir au moins 8 caractères !";
        header("location:profil.php");
        exit();
    }

    if($nouveau_mdp !== $confirmer_mdp){
        $_SESSION['erreur'] = "Les mots de passe ne correspondent pas !";
        header("location:profil.php");
        exit();
    }

    $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = :mdp WHERE id_utilisateur = :id");
    $stmt->execute([':mdp' => $hash, ':id' => $id_utilisateur]);

    $_SESSION['succes'] = "Mot de passe modifié avec succès !";
    header("location:profil.php");
    exit();
}

header("location:profil.php");
exit();
?>