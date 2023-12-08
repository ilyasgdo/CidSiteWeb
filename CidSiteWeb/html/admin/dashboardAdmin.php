<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un modérateur
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'moderateur') {
    header("Location: ../user/connexion.html");
    exit();
}

require_once('../../assets/php/pdo.php');

// Fonction pour récupérer les statistiques du site
function getSiteStatistics($pdo) {
    $statistics = array();

    // Nombre total d'inscrits
    $queryUsers = $pdo->query("SELECT COUNT(*) AS total_users FROM utilisateur");
    $statistics['total_users'] = $queryUsers->fetchColumn();

    // Nombre total d'événements
    $queryEvents = $pdo->query("SELECT COUNT(*) AS total_events FROM evenement");
    $statistics['total_events'] = $queryEvents->fetchColumn();

    // Nombre total de photos
    $queryPhotos = $pdo->query("SELECT COUNT(*) AS total_photos FROM photos");
    $statistics['total_photos'] = $queryPhotos->fetchColumn();

    return $statistics;
}

// Récupérer les statistiques du site
$siteStatistics = getSiteStatistics($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord administrateur</title>
</head>
<body>
    <h1>Tableau de bord administrateur</h1>

    <?php if (isset($_SESSION['user_email'])) : ?>
        <p>Bienvenue, <?php echo $_SESSION['user_email']; ?> !</p>
        <a href="logout.php"><button>Déconnexion</button></a>

        <!-- Tableau de statistiques -->
        <h2>Statistiques du site</h2>
        <table border="1">
            <tr>
                <th>Statistique</th>
                <th>Valeur</th>
            </tr>
            <tr>
                <td>Nombre total d'inscrits</td>
                <td><?php echo $siteStatistics['total_users']; ?></td>
            </tr>
            <tr>
                <td>Nombre total d'événements</td>
                <td><?php echo $siteStatistics['total_events']; ?></td>
            </tr>
            <tr>
                <td>Nombre total de photos</td>
                <td><?php echo $siteStatistics['total_photos']; ?></td>
            </tr>
        </table>
    <?php else : ?>
        <p>Vous n'êtes pas connecté.</p>
        <a href="connexion.php"><button>Connexion</button></a>
    <?php endif; ?>

    <!-- Liens vers les pages de gestion -->
    <a href="gestion/gestionMembres.php"><button>Gestion des membres</button></a>
    <a href="gestion/event/gestionEvents.php"><button>Gestion des evenements</button></a>
    <a href="gestion/gestionPhotos.php"><button>Gestion des photos</button></a>
    <a href="gestion/gestionDoublons.php"><button>Gestion des Doublons</button></a>
    <a href="gestion/afficheContact.php"><button>Afficher les contact</button></a>
    <a href="../../index.php" class="">Accueil</a>
     
</body>
</html>
