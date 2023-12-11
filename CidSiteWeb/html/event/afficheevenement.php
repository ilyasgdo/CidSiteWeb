<?php
session_start();

require_once('../../assets/php/pdo.php');

// Vérifiez si l'ID de l'événement est passé dans l'URL
if (isset($_GET['id'])) {
    $eventId = $_GET['id'];

    // Récupérez les détails de l'événement spécifique en utilisant son ID
    $eventQuery = $pdo->prepare("SELECT e.*, u.nom, p.fichier_photo 
                                FROM evenement e 
                                JOIN utilisateur u ON e.ID_utilisateur = u.ID_utilisateur 
                                JOIN photos p ON e.ID_evenement = p.ID_evenement 
                                WHERE e.ID_evenement = :eventId");
    $eventQuery->bindValue(':eventId', $eventId, PDO::PARAM_INT);
    $eventQuery->execute();

    // Vérifiez si l'événement existe
    if ($eventQuery->rowCount() > 0) {
        $event = $eventQuery->fetch(PDO::FETCH_ASSOC);
    } else {
        // Redirigez vers une page d'erreur ou la page principale si l'événement n'existe pas
        header('Location: index.php');
        exit();
    }
} else {
    // Redirigez vers une page d'erreur ou la page principale si l'ID de l'événement n'est pas fourni
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afficher l'Événement</title>
    <!-- Ajoutez des liens vers les styles CSS nécessaires ici -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <h1>L'evenemnt </h1>

<div class="container">
    <h1><?php echo $event['nom_evenement']; ?></h1>
    
    <img src="data:image/jpeg;base64,<?php echo base64_encode($event['fichier_photo']); ?>" class="d-block w-100" alt="Événement <?php echo $event['nom_evenement']; ?>">

    <p>Thematique : <?php echo $event['thematique']; ?></p>
    <p>Description : <?php echo $event['description']; ?></p>
    <p>Par : <?php echo $event['nom']; ?></p>
    <p>Date : <?php echo $event['Date_evenement'] . ' ' . $event['heure']; ?></p>

    <!-- Ajoutez d'autres détails de l'événement selon vos besoins -->

    <a href="index.php" class="btn btn-primary">Retour aux Événements</a>
</div>

</body>
</html>
