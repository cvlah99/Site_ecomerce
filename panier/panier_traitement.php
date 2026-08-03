<?php
session_start();
require_once '../config/connexion_db.php';

// 1. Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header("location:../connexion/connexion.php");
    exit();
}

// 2. Fetch the id_client corresponding to this user_id if it's not set in the session yet
if (!isset($_SESSION['id_client'])) {
    $stmt_cli = $pdo->prepare("SELECT id_client FROM clients WHERE id_utilisateur = ?");
    $stmt_cli->execute([$_SESSION['user_id']]);
    $client = $stmt_cli->fetch(PDO::FETCH_ASSOC);
    
    if ($client) {
        $_SESSION['id_client'] = $client['id_client'];
    } else {
        // If no client profile is found (meaning the user is an admin or livreur)
        echo "<script>
                alert('Action refusée : Seuls les clients peuvent ajouter des articles au panier.'); 
                window.location.href='../catalogue/catalogue.php';
              </script>";
        exit();
    }
}

// 3. Check if we have the required POST data
if(!isset($_POST['action'], $_POST['id_produit'])){
    header("location:panier.php");
    exit();
}

// 4. Now it's safe to assign the variables
$id_client = $_SESSION['id_client'];
$id_produit = intval($_POST['id_produit']);
$action = $_POST['action'];

$stmt_panier = $pdo->prepare("SELECT id_panier FROM paniers WHERE id_client = :id_client");
$stmt_panier->execute([':id_client' => $id_client]);
$panier = $stmt_panier->fetch(PDO::FETCH_ASSOC);

if(!$panier){
    $stmt_create = $pdo->prepare("INSERT INTO paniers (id_client, date_creation, montant_total) VALUES (:id_client, CURDATE(), 0)");
    $stmt_create->execute([':id_client' => $id_client]);
    $id_panier = $pdo->lastInsertId();
} else {
    $id_panier = $panier['id_panier'];
}

if($action === 'ajouter'){
    $quantite = isset($_POST['quantite']) ? intval($_POST['quantite']) : 1;
    
    $stmt_check = $pdo->prepare("SELECT quantite_produit FROM panier_produits WHERE id_panier = :id_panier AND id_produit = :id_produit");
    $stmt_check->execute([':id_panier' => $id_panier, ':id_produit' => $id_produit]);
    $existe = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if($existe){
        $stmt_update = $pdo->prepare("UPDATE panier_produits SET quantite_produit = quantite_produit + :quantite WHERE id_panier = :id_panier AND id_produit = :id_produit");
        $stmt_update->execute([':quantite' => $quantite, ':id_panier' => $id_panier, ':id_produit' => $id_produit]);
    } else {
        $stmt_insert = $pdo->prepare("INSERT INTO panier_produits (id_panier, id_produit, quantite_produit) VALUES (:id_panier, :id_produit, :quantite)");
        $stmt_insert->execute([':id_panier' => $id_panier, ':id_produit' => $id_produit, ':quantite' => $quantite]);
    }
    
    $_SESSION['succes'] = "Produit ajouté au panier avec succès !";
    header("location:panier.php");
    exit();
}

if($action === 'augmenter'){
    $stmt_update = $pdo->prepare("UPDATE panier_produits SET quantite_produit = quantite_produit + 1 WHERE id_panier = :id_panier AND id_produit = :id_produit");
    $stmt_update->execute([':id_panier' => $id_panier, ':id_produit' => $id_produit]);
    header("location:panier.php");
    exit();
}

if($action === 'diminuer'){
    $stmt_check = $pdo->prepare("SELECT quantite_produit FROM panier_produits WHERE id_panier = :id_panier AND id_produit = :id_produit");
    $stmt_check->execute([':id_panier' => $id_panier, ':id_produit' => $id_produit]);
    $item = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if($item && $item['quantite_produit'] > 1){
        $stmt_update = $pdo->prepare("UPDATE panier_produits SET quantite_produit = quantite_produit - 1 WHERE id_panier = :id_panier AND id_produit = :id_produit");
        $stmt_update->execute([':id_panier' => $id_panier, ':id_produit' => $id_produit]);
    } else {
        $stmt_delete = $pdo->prepare("DELETE FROM panier_produits WHERE id_panier = :id_panier AND id_produit = :id_produit");
        $stmt_delete->execute([':id_panier' => $id_panier, ':id_produit' => $id_produit]);
    }
    header("location:panier.php");
    exit();
}

if($action === 'supprimer'){
    $stmt_delete = $pdo->prepare("DELETE FROM panier_produits WHERE id_panier = :id_panier AND id_produit = :id_produit");
    $stmt_delete->execute([':id_panier' => $id_panier, ':id_produit' => $id_produit]);
    $_SESSION['succes'] = "Produit supprimé du panier !";
    header("location:panier.php");
    exit();
}

header("location:panier.php");
exit();
?>