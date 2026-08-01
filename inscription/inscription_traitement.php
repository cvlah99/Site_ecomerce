<?php
session_start();
require_once '../config/connexion_db.php';
if(isset($_POST["nom"],$_POST["prenom"],$_POST["email"],$_POST["telephone"],$_POST["mot_de_passe"],$_POST["confirmer_mdp"])){
    $nom = trim(htmlspecialchars($_POST["nom"]));
    $prenom = trim(htmlspecialchars($_POST["prenom"]));
    $email = trim(htmlspecialchars($_POST["email"]));
    $telephone = trim(htmlspecialchars($_POST["telephone"]));
    $mot_de_passe = trim($_POST["mot_de_passe"]);
    $confirmer_mdp = trim($_POST["confirmer_mdp"]);
    if(empty($nom)){
    $_SESSION['erreur'] = "Le nom est obligatoire !";
    header("location:inscription.php");
    exit();
    } elseif(strlen($nom) < 3 || strlen($nom) > 30){
    $_SESSION['erreur'] = "Le nom doit contenir entre 3 et 30 caractères !";
    header("location:inscription.php");
    exit();
    } elseif(!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $nom)){
    $_SESSION['erreur'] = "Le nom doit contenir des lettres seulement !";
    header("location:inscription.php");
    exit();
    


}
    if(empty($prenom)){
    $_SESSION['erreur'] = "Le prénom est obligatoire !";
    header("location:inscription.php");
    exit();
    } elseif(strlen($prenom) < 3 || strlen($prenom) > 30){
    $_SESSION['erreur'] = "Le prénom doit contenir entre 3 et 30 caractères !";
    header("location:inscription.php");
    exit();
    } elseif(!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $prenom)){
    $_SESSION['erreur'] = "Le prénom doit contenir des lettres seulement !";
    header("location:inscription.php");
    exit();
}
    if(empty($email)){
    $_SESSION['erreur'] = "L'email est obligatoire !";
    header("location:inscription.php");
    exit();
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $_SESSION['erreur'] = "L'adresse email n'est pas valide !";
    header("location:inscription.php");
    exit();
}
    if(empty($telephone)){
    $_SESSION['erreur'] = "Le téléphone est obligatoire !";
    header("location:inscription.php");
    exit();
    } elseif(!preg_match('/^(\+212|0)[5-7][0-9]{8}$/', $telephone)){
    $_SESSION['erreur'] = "Le numéro de téléphone n'est pas valide ! (06XXXXXXXX ou +212XXXXXXXXX)";
    header("location:inscription.php");
    exit();
}
    if(empty($mot_de_passe)){
    $_SESSION['erreur'] = "Le mot de passe est obligatoire !";
    header("location:inscription.php");
    exit();
    } elseif(strlen($mot_de_passe) < 8){
    $_SESSION['erreur'] = "Le mot de passe doit contenir au moins 8 caractères !";
    header("location:inscription.php");
    exit();
    } elseif(!preg_match('/[A-Z]/', $mot_de_passe)){
    $_SESSION['erreur'] = "Le mot de passe doit contenir au moins une majuscule !";
    header("location:inscription.php");
    exit();
    } elseif(!preg_match('/[0-9]/', $mot_de_passe)){
    $_SESSION['erreur'] = "Le mot de passe doit contenir au moins un chiffre !";
    header("location:inscription.php");
    exit();
    } elseif(!preg_match('/[!@#$%^&*]/', $mot_de_passe)){
    $_SESSION['erreur'] = "Le mot de passe doit contenir au moins un caractère spécial (!@#$%) !";
    header("location:inscription.php");
    exit();
}
    if(empty($confirmer_mdp)){
    $_SESSION['erreur'] = "La confirmation du mot de passe est obligatoire !";
    header("location:inscription.php");
    exit();
    } elseif($mot_de_passe !== $confirmer_mdp){
    $_SESSION['erreur'] = "Les mots de passe ne correspondent pas !";
    header("location:inscription.php");
    exit();
    }
    $stmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);

    if($stmt->rowCount() > 0){
    $_SESSION['erreur'] = "Cet email est déjà utilisé par un autre compte !";
    header("location:inscription.php");
    exit();
}
$mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
$date_inscription = date('Y-m-d');

$stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, telephone, date_inscription, statut) 
VALUES (:nom, :prenom, :email, :mot_de_passe, :telephone, :date_inscription, :statut)");

$stmt->execute([
    ':nom' => $nom,
    ':prenom' => $prenom,
    ':email' => $email,
    ':mot_de_passe' => $mot_de_passe_hash,
    ':telephone' => $telephone,
    ':date_inscription' => $date_inscription,
    ':statut' => 'actif'
]);

$id_utilisateur = $pdo->lastInsertId();

$stmt = $pdo->prepare("INSERT INTO clients (id_utilisateur) VALUES (:id_utilisateur)");
$stmt->execute([':id_utilisateur' => $id_utilisateur]);
$id_client = $pdo->lastInsertId();
$_SESSION['user_id'] = $id_utilisateur;
$_SESSION['id_client'] = $id_client;
$_SESSION['user_nom'] = $nom;
$_SESSION['user_prenom'] = $prenom;
$_SESSION['user_email'] = $email;
$_SESSION['user_role'] = 'client';

$_SESSION['succes'] = "Votre compte a été créé avec succès !";
header("location:../acceulle/acceulle.php");
exit();
}
else{
    header("location:inscription.php");
    exit();
}
?>