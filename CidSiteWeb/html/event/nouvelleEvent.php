<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../user/connection.html");
    exit();
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un événement</title>
</head>
<body>
    <h1>Créer un événement</h1>

    <!-- Formulaire pour créer un événement avec téléversement de photo -->
    <form action="../../assets/php/uploadEvent.php" method="post" enctype="multipart/form-data">
        <label for="thematique">Thématique :</label>
        <input type="text" id="thematique" name="thematique" required><br>

        <label for="date_evenement">Date de l'événement :</label>
        <input type="date" id="date_evenement" name="date_evenement" required><br>

        <label for="heure">Heure de l'événement :</label>
        <input type="time" id="heure" name="heure" required><br>

        <label for="description">Description :</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="nom_evenement">Nom de l'événement :</label>
        <input type="text" id="nom_evenement" name="nom_evenement" required><br>

        <!-- Champ pour téléverser une photo -->
        <label for="fichier_photo">Téléverser une photo :</label>
        <input type="file" id="fichier_photo" name="fichier_photo" accept="image/*"><br>

        <input type="submit" value="Créer l'événement">
    </form>

</body>
</html>
