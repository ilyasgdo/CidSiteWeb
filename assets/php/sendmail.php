<?php
require('./pdo.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $userID = 1; // Utilisateur 1 est le propriétaire de la photo

    // Traitement du fichier
    $uploadDirectory = 'uploads/'; // Dossier où les photos seront stockées
    $uploadFile = $uploadDirectory . basename($_FILES['photo']['name']);

    // Vérifier si l'utilisateur existe avant l'insertion
    $userExists = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE ID_utilisateur = ?");
    $userExists->execute([$userID]);

    if ($userExists->fetchColumn() > 0) {
        // L'utilisateur existe, procéder à l'insertion
        $stmt = $pdo->prepare("INSERT INTO photos (titre, description, ID_utilisateur, fichier_photo) VALUES (?, ?, ?, ?)");
        $stmt->execute([$titre, $description, $userID, file_get_contents($uploadFile)]);

        echo "La photo a été envoyée avec succès!";
    } else {
        echo "Erreur : L'utilisateur avec l'ID $userID n'existe pas.";
    }
}
?>
