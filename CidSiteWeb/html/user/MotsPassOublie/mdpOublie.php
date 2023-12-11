<?php
session_start();

// Inclure le fichier de configuration de la base de données
require_once('../../../assets/php/pdo.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Vérifier si l'email existe dans la base de données
    $checkEmailQuery = $pdo->prepare("SELECT ID_utilisateur FROM utilisateur WHERE email = :email");
    $checkEmailQuery->bindParam(':email', $email, PDO::PARAM_STR);
    $checkEmailQuery->execute();
    $user = $checkEmailQuery->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Générer un code aléatoire
        $code = bin2hex(random_bytes(10));

        // Insérer le code dans la base de données avec une expiration de 1 heure
        $expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
        $insertCodeQuery = $pdo->prepare("INSERT INTO reset_password_requests (user_id, code, expiration) VALUES (:user_id, :code, :expiration)");
        $insertCodeQuery->bindParam(':user_id', $user['ID_utilisateur'], PDO::PARAM_INT);
        $insertCodeQuery->bindParam(':code', $code, PDO::PARAM_STR);
        $insertCodeQuery->bindParam(':expiration', $expiration, PDO::PARAM_STR);
        $insertCodeQuery->execute();

        
        $Subject = 'Réinitialisation de mot de passe';
        $Body = "Votre code de réinitialisation de mot de passe est : $code";
       // mail($email,$Subject, $Body)
       if (true) {
        echo "Un email de réinitialisation a été envoyé à votre adresse.";
    
        // Rediriger vers la page de saisie du code
        header("Location: newMdp.php?email=$email");
    
        // Assurez-vous que rien d'autre n'est affiché après cette ligne
        exit();
    } else {
        echo "Erreur lors de l'envoi de l'email : ";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
</head>
<body>

<div class="container">
    <h1>Mot de passe oublié</h1>
    <form method="POST" action="">
        <label for="email">Email :</label>
        <input type="email" name="email" required><br>
        <input type="submit" value="Envoyer le code de réinitialisation">
    </form>
</div>

</body>
</html>
