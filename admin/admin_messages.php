<?php
session_start();
require_once '../config/connexion_db.php';

// Sécurité : Vérifier que l'utilisateur est bien un admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("location: ../connexion/connexion.php");
    exit();
}

// Gestion des actions (Marquer comme lu ou Supprimer)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_message = intval($_GET['id']);
    
    if ($_GET['action'] === 'lu') {
        $stmt = $pdo->prepare("UPDATE messages_contact SET statut = 'lu' WHERE id_message = ?");
        $stmt->execute([$id_message]);
    } elseif ($_GET['action'] === 'supprimer') {
        $stmt = $pdo->prepare("DELETE FROM messages_contact WHERE id_message = ?");
        $stmt->execute([$id_message]);
    }
    
    // Redirection pour éviter de renvoyer l'action en rafraîchissant la page
    header("Location: admin_messages.php");
    exit();
}

// Récupérer tous les messages (les plus récents en premier)
$stmt = $pdo->prepare("SELECT * FROM messages_contact ORDER BY date_envoi DESC");
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Messages Contact</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Lato', sans-serif; }
        .navbar-admin { background-color: #2C4A3B; }
        .navbar-admin .navbar-brand, .navbar-admin .nav-link { color: white !important; }
        .card { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .badge-nouveau { background-color: #C5A880; color: white; }
        .badge-lu { background-color: #6c757d; color: white; }
        .table align-middle td { padding: 1rem 0.5rem; }
    </style>
</head>
<body>

<!-- Navbar Admin Basique -->
<nav class="navbar navbar-expand-lg navbar-admin mb-4 py-3 shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-bold" href="admin_dashboard.php" style="font-family: 'Playfair Display', serif;">
            SoinVital <span style="font-size: 0.9rem; font-weight: normal; opacity: 0.8;">| Admin</span>
        </a>
        <div class="d-flex gap-3">
            <a href="admin_dashboard.php" class="btn btn-outline-light btn-sm">Retour au Dashboard</a>
            <a href="../deconnexion/deconnexion.php" class="btn btn-light btn-sm text-danger">Déconnexion</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark" style="font-family: 'Playfair Display', serif;">Boîte de réception</h2>
        <span class="text-muted">Total: <?php echo count($messages); ?> message(s)</span>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Statut</th>
                            <th>Date</th>
                            <th>Expéditeur</th>
                            <th>Sujet</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($messages) > 0): ?>
                            <?php foreach ($messages as $msg): ?>
                                <tr class="<?php echo $msg['statut'] === 'nouveau' ? 'table-warning bg-opacity-10' : ''; ?>">
                                    <td class="ps-4">
                                        <?php if ($msg['statut'] === 'nouveau'): ?>
                                            <span class="badge badge-nouveau px-2 py-1">Nouveau</span>
                                        <?php else: ?>
                                            <span class="badge badge-lu px-2 py-1">Lu</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted fw-semibold">
                                            <?php echo date('d/m/Y H:i', strtotime($msg['date_envoi'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?php echo htmlspecialchars($msg['nom']); ?></div>
                                        <small class="text-muted"><?php echo htmlspecialchars($msg['email']); ?></small>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($msg['sujet']); ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <!-- Bouton Voir (Ouvre un Modal) -->
                                        <button class="btn btn-sm btn-outline-success me-1" data-bs-toggle="modal" data-bs-target="#modalMsg<?php echo $msg['id_message']; ?>">
                                            <i class="bi bi-eye"></i> Voir
                                        </button>
                                        
                                        <!-- Bouton Marquer comme lu -->
                                        <?php if ($msg['statut'] === 'nouveau'): ?>
                                            <a href="?action=lu&id=<?php echo $msg['id_message']; ?>" class="btn btn-sm btn-outline-secondary me-1" title="Marquer comme lu">
                                                <i class="bi bi-check2-all"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Bouton Supprimer -->
                                        <a href="?action=supprimer&id=<?php echo $msg['id_message']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?');" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Modal pour afficher le message complet -->
                                <div class="modal fade" id="modalMsg<?php echo $msg['id_message']; ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold">Message de <?php echo htmlspecialchars($msg['nom']); ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3 border-bottom pb-2">
                                                    <small class="text-muted d-block">Sujet :</small>
                                                    <span class="fw-bold"><?php echo htmlspecialchars($msg['sujet']); ?></span>
                                                </div>
                                                <div class="mb-3 border-bottom pb-2">
                                                    <small class="text-muted d-block">Email de contact :</small>
                                                    <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="text-success text-decoration-none">
                                                        <?php echo htmlspecialchars($msg['email']); ?>
                                                    </a>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block mb-2">Message :</small>
                                                    <p style="white-space: pre-wrap; font-size: 0.95rem; color: #333;"><?php echo htmlspecialchars($msg['message']); ?></p>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fermer</button>
                                                <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="btn btn-success btn-sm">
                                                    <i class="bi bi-reply me-1"></i> Répondre par email
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Aucun message reçu pour le moment.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>