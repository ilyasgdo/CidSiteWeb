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

// Récupérer l'ID de la promotion de l'utilisateur
try {
    $promotionQuery = $pdo->prepare("SELECT ID_promotion FROM utilisateur WHERE ID_utilisateur = :user_id");
    $promotionQuery->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $promotionQuery->execute();
    $result = $promotionQuery->fetch(PDO::FETCH_ASSOC);

    if (!$result) {
        // Gérer le cas où l'utilisateur n'a pas d'ID de promotion
        echo "L'utilisateur n'a pas d'ID de promotion.";
        exit();
    }

    $promotion_id = $result['ID_promotion'];
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit();
}

// Récupérer les autres données du formulaire
$titre = $_POST['titre'];
$description = $_POST['description'];
$fichier_photo = $_FILES['fichier_photo']['tmp_name'];

// Convertir le fichier photo en format LONGBLOB
$fichier_photo_contenu = file_get_contents($fichier_photo);

// Insérer les données dans la base de données
try {
    $query = $pdo->prepare("INSERT INTO photos (titre, description, date_creation, fichier_photo, ID_utilisateur, ID_promotion)
                            VALUES (:titre, :description, CURRENT_TIMESTAMP, :fichier_photo, :user_id, :promotion_id)");
    
    $query->bindParam(':titre', $titre, PDO::PARAM_STR);
    $query->bindParam(':description', $description, PDO::PARAM_STR);
    $query->bindParam(':fichier_photo', $fichier_photo_contenu, PDO::PARAM_LOB);
    $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindParam(':promotion_id', $promotion_id, PDO::PARAM_INT);

    $query->execute();

    echo "La photo a été ajoutée avec succès !";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
