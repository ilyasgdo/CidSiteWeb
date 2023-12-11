<?php
session_start();

// Inclure le fichier de configuration de la base de données
require_once('../../../assets/php/pdo.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    echo "L'email n'est pas spécifié.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $nouveauMotDePasse = $_POST['nouveauMotDePasse'];

    // Vérifier si le code est valide
    $checkCodeQuery = $pdo->prepare("SELECT user_id FROM reset_password_requests WHERE code = :code AND expiration > NOW()");
    $checkCodeQuery->bindParam(':code', $code, PDO::PARAM_STR);
    $checkCodeQuery->execute();
    $user = $checkCodeQuery->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Mettre à jour le mot de passe
        $hashNouveauMotDePasse = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);
        $updatePasswordQuery = $pdo->prepare("UPDATE utilisateur SET password = :password WHERE ID_utilisateur = :user_id");
        $updatePasswordQuery->bindParam(':password', $hashNouveauMotDePasse, PDO::PARAM_STR);
        $updatePasswordQuery->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
        $updatePasswordQuery->execute();

        // Supprimer le code de la base de données
        $deleteCodeQuery = $pdo->prepare("DELETE FROM reset_password_requests WHERE code = :code");
        $deleteCodeQuery->bindParam(':code', $code, PDO::PARAM_STR);
        $deleteCodeQuery->execute();

        echo "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter avec votre nouveau mot de passe.";
    } else {
        echo "Le code saisi est invalide ou a expiré.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changer le mot de passe oublié</title>
</head>
<body>

<div class="container">
    <h1>Changer le mot de passe oublié</h1>
    <form method="POST" action="">
        <label for="code">Code de réinitialisation :</label>
        <input type="text" name="code" required><br>
        <label for="nouveauMotDePasse">Nouveau mot de passe :</label>
        <input type="password" name="nouveauMotDePasse" required><br>
        <input type="submit" value="Réinitialiser le mot de passe">
    </form>
</div>

</body>
</html>
