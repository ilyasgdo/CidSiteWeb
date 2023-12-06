<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un modérateur
if (!isset($_SESSION['user_email']) || $_SESSION['user_role'] !== 'moderateur') {
    header("Location: ../../../user/connexion.html");
    exit();
}

require_once('../../../assets/php/pdo.php');

// Traiter les actions si une action est définie dans l'URL
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    switch ($action) {
        case 'modifier':
            if (isset($_GET['id'])) {
                $photo_id = $_GET['id'];
                modifierPhoto($photo_id);
            }
            break;
        case 'supprimer':
            if (isset($_GET['id'])) {
                $photo_id = $_GET['id'];
                supprimerPhoto($photo_id);
            }
            break;
        
        // Ajoutez d'autres cas selon vos besoins
    }

    // Arrêtez l'exécution du script après avoir traité l'action
    exit();
}

// Reste du code PHP pour afficher les photos
afficherPhotos();

function afficherPhotos() {
    global $pdo;

    // Récupérer les photos depuis la base de données
    $query = $pdo->query("SELECT * FROM photos ORDER BY date_creation DESC");

    echo "<h1>Gestion des Photos</h1>";
    echo "<table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Titre</th>
                    <th>Description</th>
                    <th>Validé</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['date_creation'] . "</td>";
        echo "<td>" . $row['titre'] . "</td>";
        echo "<td>" . $row['description'] . "</td>";
       
        echo "<td>
                <a href='?action=modifier&id=" . $row['ID_photo'] . "'>Modifier</a> |
                <a href='?action=supprimer&id=" . $row['ID_photo'] . "'>Supprimer</a> |
                
              </td>";
        echo "</tr>";
    }

    echo "</tbody>
        </table>";
}

function modifierPhoto($photo_id) {
    global $pdo;

    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les valeurs soumises
        $nouveauTitre = $_POST['nouveauTitre'];
        $nouvelleDescription = $_POST['nouvelleDescription'];

        // Mettre à jour la photo dans la base de données
        $query = $pdo->prepare("UPDATE photos SET titre = :titre, description = :description WHERE ID_photo = :id");
        $query->bindParam(':titre', $nouveauTitre, PDO::PARAM_STR);
        $query->bindParam(':description', $nouvelleDescription, PDO::PARAM_STR);
        $query->bindParam(':id', $photo_id, PDO::PARAM_INT);

        try {
            $query->execute();
            echo "La photo a été modifiée avec succès.";
        } catch (PDOException $e) {
            echo "Erreur lors de la modification de la photo : " . $e->getMessage();
        }
    }

    // Récupérer les détails actuels de la photo
    $query = $pdo->prepare("SELECT * FROM photos WHERE ID_photo = :id");
    $query->bindParam(':id', $photo_id, PDO::PARAM_INT);
    $query->execute();
    $photo = $query->fetch(PDO::FETCH_ASSOC);

    // Afficher le formulaire de modification avec les détails actuels
    echo "<h2>Modifier la photo</h2>";
    echo "<form method='POST' action='?action=modifier&id={$photo['ID_photo']}'>";
    echo "<label for='nouveauTitre'>Nouveau Titre:</label>";
    echo "<input type='text' name='nouveauTitre' value='{$photo['titre']}' required><br>";

    echo "<label for='nouvelleDescription'>Nouvelle Description:</label>";
    echo "<textarea name='nouvelleDescription' required>{$photo['description']}</textarea><br>";

    echo "<input type='submit' value='Enregistrer les modifications'>";
    echo "</form>";
}

function supprimerPhoto($photo_id) {
    global $pdo;

    // Supprimer la photo de la base de données
    $query = $pdo->prepare("DELETE FROM photos WHERE ID_photo = :id");
    $query->bindParam(':id', $photo_id, PDO::PARAM_INT);

    try {
        $query->execute();
        echo "La photo a été supprimée avec succès.";
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression de la photo : " . $e->getMessage();
    }
}


?>
