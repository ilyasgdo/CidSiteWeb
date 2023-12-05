<?php
session_start();
require_once('./pdo.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: ../../html/connection.html");
    exit();
}

// Récupérer l'ID de l'utilisateur connecté
$user_id = $_SESSION['user_id'];

// Récupérer les données du formulaire
$thematique = $_POST['thematique'];
$date_evenement = $_POST['date_evenement'];
$heure = $_POST['heure'];
$description = $_POST['description'];
$nom_evenement = $_POST['nom_evenement'];
$fichier_photo = $_FILES['fichier_photo']['tmp_name'];

// Convertir le fichier photo en format LONGBLOB
$fichier_photo_contenu = file_get_contents($fichier_photo);

// Insérer les données dans la base de données
try {
    // Insérer les données de l'événement
    $query = $pdo->prepare("INSERT INTO evenement (thematique, Date_evenement, heure, description, nom_evenement, ID_utilisateur, approuve)
                            VALUES (:thematique, :date_evenement, :heure, :description, :nom_evenement, :user_id, false)");
    
    $query->bindParam(':thematique', $thematique, PDO::PARAM_STR);
    $query->bindParam(':date_evenement', $date_evenement, PDO::PARAM_STR);
    $query->bindParam(':heure', $heure, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->bindParam(':nom_evenement', $nom_evenement, PDO::PARAM_STR);
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);

    $query->execute();

    // Récupérer l'ID de l'événement créé
    $evenement_id = $pdo->lastInsertId();

    // Insérer les données de la photo liée à l'événement
    $photoQuery = $pdo->prepare("INSERT INTO photos (titre, description, date_creation, fichier_photo, ID_utilisateur, ID_promotion, ID_evenement)
                            VALUES (:titre, :description, CURRENT_TIMESTAMP, :fichier_photo, :user_id, :promotion_id, :evenement_id)");
    
    $photoQuery->bindParam(':titre', $nom_evenement, PDO::PARAM_STR);
    $photoQuery->bindParam(':description', $description, PDO::PARAM_STR);
    $photoQuery->bindParam(':fichier_photo', $fichier_photo_contenu, PDO::PARAM_LOB);
    $photoQuery->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $photoQuery->bindParam(':promotion_id', $promotion_id, PDO::PARAM_INT);
    $photoQuery->bindParam(':evenement_id', $evenement_id, PDO::PARAM_INT);

    $photoQuery->execute();

    echo "L'événement a été créé avec succès, et la photo a été ajoutée !";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
