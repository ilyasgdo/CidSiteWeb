<?php
session_start();

require_once('../../assets/php/pdo.php');

// Récupérer les 16 derniers événements de la base avec le nom de l'utilisateur et les images associées
$eventsQuery = $pdo->query("SELECT e.*, u.nom, p.fichier_photo 
                            FROM evenement e 
                            JOIN utilisateur u ON e.ID_utilisateur = u.ID_utilisateur 
                            JOIN photos p ON e.ID_evenement = p.ID_evenement 
                            ORDER BY e.date_evenement DESC LIMIT 16");
$latestEvents = $eventsQuery->fetchAll(PDO::FETCH_ASSOC);

// Récupérer toutes les événements avec le nom de l'utilisateur et les images associées pour le deuxième carousel
$allEventsQuery = $pdo->query("SELECT e.*, u.nom, p.fichier_photo 
                                FROM evenement e 
                                JOIN utilisateur u ON e.ID_utilisateur = u.ID_utilisateur 
                                JOIN photos p ON e.ID_evenement = p.ID_evenement 
                                ORDER BY e.date_evenement DESC");
$allEvents = $allEventsQuery->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire de tri
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tri'])) {
    $tri = $_GET['tri'];

    switch ($tri) {
        case 'date_debut':
            $allEventsQuery = $pdo->query("SELECT e.*, u.nom, p.fichier_photo 
                                            FROM evenement e 
                                            JOIN utilisateur u ON e.ID_utilisateur = u.ID_utilisateur 
                                            JOIN photos p ON e.ID_evenement = p.ID_evenement 
                                            ORDER BY e.Date_evenement ASC");
            break;
        case 'date_fin':
            $allEventsQuery = $pdo->query("SELECT e.*, u.nom, p.fichier_photo 
                                            FROM evenement e 
                                            JOIN utilisateur u ON e.ID_utilisateur = u.ID_utilisateur 
                                            JOIN photos p ON e.ID_evenement = p.ID_evenement 
                                            ORDER BY e.Date_evenementt DESC");
            break;
        case 'nom_utilisateur':
            // Utilisez une clause LIKE pour effectuer une recherche par nom d'utilisateur
            $rechercheNom = isset($_GET['recherche_nom']) ? $_GET['recherche_nom'] : '';
            $allEventsQuery = $pdo->prepare("SELECT e.*, u.nom, p.fichier_photo 
                                            FROM evenement e 
                                            JOIN utilisateur u ON e.ID_utilisateur = u.ID_utilisateur 
                                            JOIN photos p ON e.ID_evenement = p.ID_evenement 
                                            WHERE u.nom LIKE :recherche ORDER BY u.nom ASC");
            $allEventsQuery->bindValue(':recherche', '%' . $rechercheNom . '%', PDO::PARAM_STR);
            $allEventsQuery->execute();
            break;
        case 'titre':
            // Utilisez une clause LIKE pour effectuer une recherche par titre
            $rechercheTitre = isset($_GET['recherche_titre']) ? $_GET['recherche_titre'] : '';
            $allEventsQuery = $pdo->prepare("SELECT e.*, u.nom, p.fichier_photo 
                                            FROM evenement e 
                                            JOIN utilisateur u ON e.ID_utilisateur = u.ID_utilisateur 
                                            JOIN photos p ON e.ID_evenement = p.ID_evenement 
                                            WHERE e.nom_evenement LIKE :recherche ORDER BY e.nom_evenement  ASC");
            $allEventsQuery->bindValue(':recherche', '%' . $rechercheTitre . '%', PDO::PARAM_STR);
            $allEventsQuery->execute();
            break;
    }

    // Vérifiez si la requête a réussi avant d'accéder à fetchAll
    if ($allEventsQuery) {
        // Récupérer la nouvelle liste de tous les événements après le tri
        $allEvents = $allEventsQuery->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Événements</title>
    <style>
        .carousel-item img {
            max-width: 200px;
            width: 100%;
            object-fit: cover;
            margin: auto;
        }
    </style>
    <!-- Ajoutez des liens vers les styles CSS et les bibliothèques JavaScript nécessaires ici -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</head>
<body>

<div class="container">
    <h1>Les Événements</h1>

    <!-- Première section avec les 4 carousels alignés horizontalement -->
    <div class="row">
        <?php for ($i = 0; $i < 4; $i++): ?>
            <div class="col-md-3">
                <h2>Carousel <?php echo $i + 1; ?></h2>
                <div id="carousel<?php echo $i + 1; ?>" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php
                        $start = $i * 4;
                        $end = ($i + 1) * 4;
                        for ($j = $start; $j < $end; $j++): ?>
                            <div class="carousel-item <?php echo ($j === $start) ? 'active' : ''; ?>">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($latestEvents[$j]['fichier_photo']); ?>" class="d-block w-100" alt="Événement <?php echo $j + 1; ?>">
                                <div class="carousel-caption d-none d-md-block" style="color: black;">
                                    <h5><?php echo $latestEvents[$j]['titre']; ?></h5>
                                    <p><?php echo $latestEvents[$j]['description']; ?></p>
                                    <p>Par : <?php echo $latestEvents[$j]['nom']; ?></p>
                                    <p>Date : <?php echo $latestEvents[$j]['Date_evenement'] . ' ' . $latestEvents[$j]['heure']; ?></p>
                                    <a href="afficheevenement.php?id=<?php echo $latestEvents[$j]['ID_evenement']; ?>" class="btn btn-primary">Afficher</a>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?php echo $i + 1; ?>" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carousel<?php echo $i + 1; ?>" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
        <?php endfor; ?>
    </div>

    <!-- Deuxième section avec un carousel de tous les événements et un formulaire de tri -->
    <h2>Tous les Événements</h2>
    <div id="allEventsCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach ($allEvents as $index => $event): ?>
                <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($event['fichier_photo']); ?>" class="d-block w-100" alt="Événement <?php echo $index + 1; ?>">
                    <div class="carousel-caption d-none d-md-block" style="color: black;">
                        <h5><?php echo $event['titre']; ?></h5>
                        <p><?php echo $event['description']; ?></p>
                        <p>Par : <?php echo $event['nom']; ?></p>
                        <p>Date : <?php echo $event['Date_evenement'] . ' ' . $event['heure']; ?></p>
                        <a href="afficheevenement.php?id=<?php echo $event['ID_evenement']; ?>" class="btn btn-primary">Afficher</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#allEventsCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#allEventsCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Formulaire de tri -->
    <h2>Trier les Événements</h2>
    <form action="" method="GET">
        <label for="tri">Trier par :</label>
        <select name="tri" id="tri">
            <option value="date_debut">Date de début - Date de fin</option>
            <option value="nom_utilisateur">Nom de l'utilisateur</option>
            <option value="titre">Titre</option>
        </select>

        <!-- Champ de recherche par nom d'utilisateur -->
        <?php if (isset($_GET['tri']) && $_GET['tri'] === 'nom_utilisateur'): ?>
            <label for="recherche_nom">Nom d'utilisateur :</label>
            <input type="text" name="recherche_nom" id="recherche_nom" placeholder="Entrez le nom d'utilisateur">
        <?php endif; ?>

        <!-- Champ de recherche par titre -->
        <?php if (isset($_GET['tri']) && $_GET['tri'] === 'titre'): ?>
            <label for="recherche_titre">Titre :</label>
            <input type="text" name="recherche_titre" id="recherche_titre" placeholder="Entrez le titre">
        <?php endif; ?>

        <!-- Champs de recherche par date de début et date de fin -->
        <?php if (isset($_GET['tri']) && in_array($_GET['tri'], ['date_debut', 'date_fin'])): ?>
            <label for="date_debut">Date de début :</label>
            <input type="date" name="date_debut" id="date_debut">

            <label for="date_fin">Date de fin :</label>
            <input type="date" name="date_fin" id="date_fin">
        <?php endif; ?>

        <!-- Bouton de soumission -->
        <input type="submit" value="Trier">
    </form>
</div>

</body>
</html>
