<?php
session_start();
require_once('./pdo.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    try {
        $query = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $query->bindParam(':email', $email, PDO::PARAM_STR);
        $query->execute();
        $user = $query->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Authentification réussie
            $_SESSION['user_id'] = $user['ID_utilisateur'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            // Définir une durée de vie pour la session (30 minutes)
            $session_duration = 30 * 60; // en secondes
            session_set_cookie_params($session_duration);
            session_regenerate_id(true); // Régénérer l'ID de session pour des raisons de sécurité

            // Rediriger vers la page d'accueil ou une autre page après la connexion
            header("Location: ../../index.php");
            exit();
        } else {
            echo "Identifiants incorrects. Veuillez réessayer.";
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
