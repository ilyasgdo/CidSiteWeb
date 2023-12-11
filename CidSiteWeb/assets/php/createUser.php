<?php
require_once('./pdo.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les valeurs du formulaire
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $email = $_POST["email"];
    $parcours = $_POST["parcours"];
    $parcours_professionnel = $_POST["parcours_professionnel"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $annee_promotion = $_POST["annee_promotion"];

    try {
        // Récupérer l'ID de la promotion en fonction de l'année
        $query = $pdo->prepare("SELECT ID_promotion FROM promotion WHERE annee_promotion = :annee_promotion");
        $query->bindParam(':annee_promotion', $annee_promotion, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $id_promotion = $result['ID_promotion'];

            // Insérer l'utilisateur dans la base de données
            $insertQuery = $pdo->prepare("INSERT INTO utilisateur (nom, prenom, email, Parcours, Parcours_Professionnel, password, role, ID_promotion)
                VALUES (:nom, :prenom, :email, :parcours, :parcours_professionnel, :password, 'user', :id_promotion)");
            
            $insertQuery->bindParam(':nom', $nom, PDO::PARAM_STR);
            $insertQuery->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $insertQuery->bindParam(':email', $email, PDO::PARAM_STR);
            $insertQuery->bindParam(':parcours', $parcours, PDO::PARAM_STR);
            $insertQuery->bindParam(':parcours_professionnel', $parcours_professionnel, PDO::PARAM_STR);
            $insertQuery->bindParam(':password', $password, PDO::PARAM_STR);
            $insertQuery->bindParam(':id_promotion', $id_promotion, PDO::PARAM_INT);

            $insertQuery->execute();

            echo "Utilisateur créé avec succès !";
            header("Location: ../../index.php");
        } else {
            echo "Année de promotion non trouvée.";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
