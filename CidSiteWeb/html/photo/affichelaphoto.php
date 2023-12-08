<?php
session_start();

require_once('../../assets/php/pdo.php');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $photoId = $_GET['id'];

    // Récupérer les détails de la photo
    $photoQuery = $pdo->prepare("SELECT p.*, u.nom FROM photos p JOIN utilisateur u ON p.ID_utilisateur = u.ID_utilisateur WHERE p.ID_photo = :photoId");
    $photoQuery->bindParam(':photoId', $photoId, PDO::PARAM_INT);
    $photoQuery->execute();
    $photoDetails = $photoQuery->fetch(PDO::FETCH_ASSOC);

    if (!$photoDetails) {
        echo "Photo non trouvée.";
        exit();
    }
} else {
    echo "ID de photo non spécifié.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Afficher la Photo</title>
    <!-- Ajoutez des liens vers les styles CSS nécessaires ici -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>

<div class="container">
    <h1>Détails de la Photo</h1>

    <div>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($photoDetails['fichier_photo']); ?>" class="d-block mx-auto" alt="Photo">
        <div>
            <h2><?php echo $photoDetails['titre']; ?></h2>
            <p><?php echo $photoDetails['description']; ?></p>
            <p>Par : <?php echo $photoDetails['nom']; ?></p>
            <p>Date : <?php echo $photoDetails['date_creation']; ?></p>
            <!-- Ajoutez d'autres détails de la photo selon vos besoins -->
        </div>
    </div>

    <a href="photos.php">Retourner à la liste des photos</a>
</div>

</body>
</html>
