<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: ../../../user/connexion.html");
    exit();
}

require_once('../../assets/php/pdo.php');

// Get user ID from the session
$userID = $_SESSION['user_id'];

// Fetch user information
$userQuery = $pdo->prepare("SELECT * FROM utilisateur WHERE ID_utilisateur = :userID");
$userQuery->bindParam(':userID', $userID, PDO::PARAM_INT);
$userQuery->execute();
$user = $userQuery->fetch(PDO::FETCH_ASSOC);

// Fetch user's photos
$photosQuery = $pdo->prepare("SELECT * FROM photos WHERE ID_utilisateur = :userID");
$photosQuery->bindParam(':userID', $userID, PDO::PARAM_INT);
$photosQuery->execute();
$photos = $photosQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's events
$eventsQuery = $pdo->prepare("SELECT * FROM evenement WHERE ID_utilisateur = :userID");
$eventsQuery->bindParam(':userID', $userID, PDO::PARAM_INT);
$eventsQuery->execute();
$events = $eventsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <h1>Bonjour, <?php echo $user['prenom']; ?></h1>
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <a href="gestionU/modifierCompte.php" class="btn btn-primary btn-block">Modifier votre identité</a>
            </div>
            <div class="col-md-4">
                <a href="gestionU/gestionEventU.php" class="btn btn-primary btn-block">Gestion de vos evenements</a>
            </div>
            <div class="col-md-4">
                <a href="gestionU/gestionPhotoU.php" class="btn btn-primary btn-block">Gestion de vos photos</a>
            </div>
            <div class="col-md-4">
                <a href="../../index.php" class="btn btn-primary btn-block">Accueil</a>
            </div>
            

        </div>
    </div>

    <!-- User information table -->
    <table class="table">
        <thead>
        <tr>
            <th>Field</th>
            <th>Value</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>User ID</td>
            <td><?php echo $user['ID_utilisateur']; ?></td>
        </tr>
        <tr>
            <td>Name</td>
            <td><?php echo $user['prenom'] . ' ' . $user['nom']; ?></td>
        </tr>
        <tr>
            <td>Email</td>
            <td><?php echo $user['email']; ?></td>
        </tr>
        <tr>
            <td>Parcours</td>
            <td><?php echo $user['Parcours']; ?></td>
        </tr>
        </tbody>
    </table>

    <h2>Mes photos</h2>
        <div id="photosCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php foreach ($photos as $index => $photo): ?>
                    <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($photo['fichier_photo']); ?>" class="d-block w-10" alt="Photo <?php echo $index + 1; ?>">
                        <div class="carousel-caption d-md-block text-dark">
                            <h5><?php echo $photo['titre']; ?></h5>
                            <p><?php echo $photo['date_creation']; ?></p>
                            <p><?php echo $photo['description']; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#photosCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#photosCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>

    <!-- User's events table -->
    <h2>My Events</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Event ID</th>
            <th>Thematique</th>
            <th>Date</th>
            <th>Time</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?php echo $event['ID_evenement']; ?></td>
                <td><?php echo $event['thematique']; ?></td>
                <td><?php echo $event['Date_evenement']; ?></td>
                <td><?php echo $event['heure']; ?></td>
                <td><?php echo $event['description']; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

</body>
</html>
