<?php
session_start();

require_once('../../assets/php/pdo.php');

// Récupérer les 16 dernières photos de la base avec le nom de l'utilisateur
$photosQuery = $pdo->query("SELECT p.*, u.nom FROM photos p JOIN utilisateur u ON p.ID_utilisateur = u.ID_utilisateur ORDER BY p.date_creation DESC LIMIT 16");
$latestPhotos = $photosQuery->fetchAll(PDO::FETCH_ASSOC);

// Récupérer toutes les photos avec le nom de l'utilisateur pour le deuxième carousel
$allPhotosQuery = $pdo->query("SELECT p.*, u.nom FROM photos p JOIN utilisateur u ON p.ID_utilisateur = u.ID_utilisateur ORDER BY p.date_creation DESC");
$allPhotos = $allPhotosQuery->fetchAll(PDO::FETCH_ASSOC);

// Traitement du formulaire de tri
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['tri'])) {
    $tri = $_GET['tri'];

    switch ($tri) {
        case 'date_debut':
            $allPhotosQuery = $pdo->query("SELECT p.*, u.nom FROM photos p JOIN utilisateur u ON p.ID_utilisateur = u.ID_utilisateur ORDER BY p.date_creation ASC");
            break;
        case 'date_fin':
            $allPhotosQuery = $pdo->query("SELECT p.*, u.nom FROM photos p JOIN utilisateur u ON p.ID_utilisateur = u.ID_utilisateur ORDER BY p.date_creation DESC");
            break;
        case 'nom_utilisateur':
            // Utilisez une clause LIKE pour effectuer une recherche par nom d'utilisateur
            $rechercheNom = isset($_GET['recherche_nom']) ? $_GET['recherche_nom'] : '';
            $allPhotosQuery = $pdo->prepare("SELECT p.*, u.nom FROM photos p JOIN utilisateur u ON p.ID_utilisateur = u.ID_utilisateur WHERE u.nom LIKE :recherche ORDER BY u.nom ASC");
            $allPhotosQuery->bindValue(':recherche', '%' . $rechercheNom . '%', PDO::PARAM_STR);
            $allPhotosQuery->execute();
            break;
        case 'titre':
            // Utilisez une clause LIKE pour effectuer une recherche par titre
            $rechercheTitre = isset($_GET['recherche_titre']) ? $_GET['recherche_titre'] : '';
            $allPhotosQuery = $pdo->prepare("SELECT p.*, u.nom FROM photos p JOIN utilisateur u ON p.ID_utilisateur = u.ID_utilisateur WHERE p.titre LIKE :recherche ORDER BY p.titre ASC");
            $allPhotosQuery->bindValue(':recherche', '%' . $rechercheTitre . '%', PDO::PARAM_STR);
            $allPhotosQuery->execute();
            break;
    }

    // Vérifiez si la requête a réussi avant d'accéder à fetchAll
    if ($allPhotosQuery) {
        // Récupérer la nouvelle liste de toutes les photos après le tri
        $allPhotos = $allPhotosQuery->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Photos</title>
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
    <h1>Les Photos</h1>

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
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($latestPhotos[$j]['fichier_photo']); ?>" class="d-block w-100" alt="Photo <?php echo $j + 1; ?>">
                                <div class="carousel-caption d-none d-md-block" style="color: black;">
                                    <h5><?php echo $latestPhotos[$j]['titre']; ?></h5>
                                    <p><?php echo $latestPhotos[$j]['description']; ?></p>
                                    <p>Par : <?php echo $latestPhotos[$j]['nom']; ?></p>
                                    <p>Date : <?php echo $latestPhotos[$j]['date_creation']; ?></p>
                                    <a href="affichelaphoto.php?id=<?php echo $latestPhotos[$j]['ID_photo']; ?>" class="btn btn-primary">Afficher</a>
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

    <!-- Deuxième section avec un carousel de toutes les photos et un formulaire de tri -->
    <h2>Toutes les Photos</h2>
    <div id="allPhotosCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <?php foreach ($allPhotos as $index => $photo): ?>
                <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($photo['fichier_photo']); ?>" class="d-block w-100" alt="Photo <?php echo $index + 1; ?>">
                    <div class="carousel-caption d-none d-md-block" style="color: black;">
                        <h5><?php echo $photo['titre']; ?></h5>
                        <p><?php echo $photo['description']; ?></p>
                        <p>Par : <?php echo $photo['nom']; ?></p>
                        <p>Date : <?php echo $photo['date_creation']; ?></p>
                        <a href="affichelaphoto.php?id=<?php echo $photo['ID_photo']; ?>" class="btn btn-primary">Afficher</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#allPhotosCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#allPhotosCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Formulaire de tri -->
    <h2>Trier les Photos</h2>
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
