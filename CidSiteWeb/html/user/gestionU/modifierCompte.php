<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_email'])) {
    header("Location: ../connection.html");
    exit();
}

require_once('../../../assets/php/pdo.php');

// Récupérer l'ID de l'utilisateur connecté
$userID = $_SESSION['user_id'];

// Récupérer les informations actuelles de l'utilisateur
$userQuery = $pdo->prepare("SELECT * FROM utilisateur WHERE ID_utilisateur = :userID");
$userQuery->bindParam(':userID', $userID, PDO::PARAM_INT);
$userQuery->execute();
$user = $userQuery->fetch(PDO::FETCH_ASSOC);

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les valeurs soumises
    $nouveauNom = $_POST['nouveauNom'];
    $nouveauPrenom = $_POST['nouveauPrenom'];
    $nouveauEmail = $_POST['nouveauEmail'];
    $nouveauParcours = $_POST['nouveauParcours'];
    $nouveauParcoursProfessionnel = $_POST['nouveauParcoursProfessionnel'];

    // Mettre à jour les informations de l'utilisateur dans la base de données
    $updateQuery = $pdo->prepare("UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, Parcours = :parcours, Parcours_Professionnel = :parcoursProfessionnel WHERE ID_utilisateur = :userID");
    $updateQuery->bindParam(':nom', $nouveauNom, PDO::PARAM_STR);
    $updateQuery->bindParam(':prenom', $nouveauPrenom, PDO::PARAM_STR);
    $updateQuery->bindParam(':email', $nouveauEmail, PDO::PARAM_STR);
    $updateQuery->bindParam(':parcours', $nouveauParcours, PDO::PARAM_STR);
    $updateQuery->bindParam(':parcoursProfessionnel', $nouveauParcoursProfessionnel, PDO::PARAM_STR);
    $updateQuery->bindParam(':userID', $userID, PDO::PARAM_INT);

    try {
        $updateQuery->execute();
        echo "Les informations du compte ont été mises à jour avec succès.";

        // Vérifier si le champ de mot de passe est rempli
        if (!empty($_POST['nouveauMotDePasse'])) {
            // Mettre à jour le mot de passe dans la base de données
            $nouveauMotDePasseHash = password_hash($_POST['nouveauMotDePasse'], PASSWORD_DEFAULT);
            $updatePasswordQuery = $pdo->prepare("UPDATE utilisateur SET password  = :motDePasse WHERE ID_utilisateur = :userID");
            $updatePasswordQuery->bindParam(':motDePasse', $nouveauMotDePasseHash, PDO::PARAM_STR);
            $updatePasswordQuery->bindParam(':userID', $userID, PDO::PARAM_INT);
            $updatePasswordQuery->execute();
            echo "Le mot de passe a été modifié avec succès.";
        }

        header("Location: ../dashboardUtilisateur.php");
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour des informations du compte : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier son Compte</title>
   
</head>
<body>

<div class="container">
    <h1>Modifier son Compte</h1>
    <form method="POST" action="">
        <label for="nouveauNom">Nouveau Nom :</label>
        <input type="text" name="nouveauNom" value="<?php echo $user['nom']; ?>" required><br>

        <label for="nouveauPrenom">Nouveau Prénom :</label>
        <input type="text" name="nouveauPrenom" value="<?php echo $user['prenom']; ?>" required><br>

        <label for="nouveauEmail">Nouveau Email :</label>
        <input type="email" name="nouveauEmail" value="<?php echo $user['email']; ?>" required><br>

        <label for="nouveauParcours">Nouveau Parcours (A,B,C) :</label>
        <input type="text" name="nouveauParcours" value="<?php echo $user['Parcours']; ?>" required><br>

        <label for="nouveauParcoursProfessionnel">Nouveau Parcours Professionnel :</label>
        <input type="text" name="nouveauParcoursProfessionnel" value="<?php echo $user['Parcours_Professionnel']; ?>"><br>

        <label for="nouveauMotDePasse">Nouveau Mot de Passe :</label>
        <input type="password" name="nouveauMotDePasse"><br>

        <input type="submit" value="Enregistrer les modifications">
    </form>
</div>



</body>
</html>
