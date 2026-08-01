<?php
session_start();
require_once '../config/connexion_db.php';

if(!isset($_SESSION['user_id'])){
    header("location:../connexion/connexion.php");
    exit();
}

if(!isset($_POST['id_panier'], $_POST['adresse'], $_POST['ville'], $_POST['telephone'])){
    header("location:commande.php");
    exit();
}

$id_client = $_SESSION['id_client'];
$id_panier = intval($_POST['id_panier']);
$adresse = trim(htmlspecialchars($_POST['adresse']));
$ville = trim(htmlspecialchars($_POST['ville']));
$telephone = trim(htmlspecialchars($_POST['telephone']));
$note = trim(htmlspecialchars($_POST['note'] ?? ''));
$total = floatval($_POST['total']);

if(empty($adresse) || empty($ville) || empty($telephone)){
    $_SESSION['erreur'] = "Veuillez remplir tous les champs obligatoires !";
    header("location:commande.php");
    exit();
}

$adresse_livraison = $adresse . ', ' . $ville;

$stmt_produits = $pdo->prepare("SELECT pp.quantite_produit, p.id_produit, p.prix
                                FROM panier_produits pp
                                JOIN produits p ON pp.id_produit = p.id_produit
                                WHERE pp.id_panier = :id_panier");
$stmt_produits->execute([':id_panier' => $id_panier]);
$produits = $stmt_produits->fetchAll(PDO::FETCH_ASSOC);

if(count($produits) === 0){
    header("location:../panier/panier.php");
    exit();
}

$stmt_commande = $pdo->prepare("INSERT INTO commandes (id_client, date_commande, statut, montant_total, adresse_livraison, note_commande, id_livreur)
                                VALUES (:id_client, CURDATE(), 'en attente', :montant_total, :adresse_livraison, :note_commande, :id_livreur)");
$stmt_commande->execute([
    ':id_client' => $id_client,
    ':montant_total' => $total,
    ':adresse_livraison' => $adresse_livraison,
    ':note_commande' => $note,
    ':id_livreur' => 1 // Assigns it directly to driver ID 1
]);

$id_commande = $pdo->lastInsertId();

foreach($produits as $produit){
    $stmt_detail = $pdo->prepare("INSERT INTO commande_produits (id_commande, id_produit, quantite, prix_unitaire)
                                  VALUES (:id_commande, :id_produit, :quantite, :prix_unitaire)");
    $stmt_detail->execute([
        ':id_commande' => $id_commande,
        ':id_produit' => $produit['id_produit'],
        ':quantite' => $produit['quantite_produit'],
        ':prix_unitaire' => $produit['prix']
    ]);

    $stmt_stock = $pdo->prepare("UPDATE stocks SET quantite_disponible = quantite_disponible - :quantite WHERE id_produit = :id_produit");
    $stmt_stock->execute([
        ':quantite' => $produit['quantite_produit'],
        ':id_produit' => $produit['id_produit']
    ]);
}

$stmt_vider = $pdo->prepare("DELETE FROM panier_produits WHERE id_panier = :id_panier");
$stmt_vider->execute([':id_panier' => $id_panier]);

$_SESSION['succes'] = "Votre commande #" . $id_commande . " a été passée avec succès !";
$_SESSION['id_commande'] = $id_commande;
header("location:../confirmation/confirmation.php");
exit();
?>