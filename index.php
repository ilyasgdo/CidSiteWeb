<?php
session_start();

// Vérifier si l'utilisateur est connecté
$user_name = "";
if (isset($_SESSION['user_email'])) {
    $user_name = $_SESSION['user_email'];
}

// Traitement de la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'accueil</title>
</head>
<body>
    <h1>Page d'accueil</h1>

    <?php if ($user_name !== "") : ?>
        <p>Bienvenue, <?php echo $user_name; ?> !</p>
        <a href="?logout=1"><button>Déconnexion</button></a>
    <?php else : ?>
        <p>Vous n'êtes pas connecté.</p>
    <?php endif; ?>

    <a href="html/photo/nouvellePhoto.php"><button>clique pour ajouter une photo</button></a>
    <a href="html/user/creeCompte.html"><button>clique pour créer un utilisateur</button></a>
    <a href="html/user/connection.html"><button>clique pour vous connecter</button></a>
    <a href="html/event/nouvelleEvent.php"><button>clique cree event</button></a>
    <a href="assets/php/contact.php"><button>contacter</button></a>
</body>
</html>
