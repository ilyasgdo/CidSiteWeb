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
    <title>Ajouter une photo</title>
</head>
<body>
    <h1>Ajouter une photo</h1>

    <!-- Formulaire pour ajouter une photo -->
    <form action="../../assets/php/uploadPhoto.php" method="post" enctype="multipart/form-data">
        <label for="titre">Titre :</label>
        <input type="text" id="titre" name="titre" required><br>

        <label for="description">Description :</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="fichier_photo">Fichier photo :</label>
        <input type="file" id="fichier_photo" name="fichier_photo" accept="image/*" required><br>

        <!-- Champ caché pour stocker l'ID de l'utilisateur connecté -->
        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

        <input type="submit" value="Ajouter la photo">
    </form>

</body>
</html>
