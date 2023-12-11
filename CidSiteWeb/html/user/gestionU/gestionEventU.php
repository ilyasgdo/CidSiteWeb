<?php
session_start();

// Vérifier si l'utilisateur est connecté et est un modérateur
if (!isset($_SESSION['user_email']) ) {
    header("Location: ../connexion.html");
    exit();
}

require_once('../../../assets/php/pdo.php');

// Traiter les actions si une action est définie dans l'URL
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    switch ($action) {
        case 'modifier':
            if (isset($_GET['id'])) {
                $evenement_id = $_GET['id'];
                modifierEvenement($evenement_id);
            }
            break;
        case 'supprimer':
            if (isset($_GET['id'])) {
                $evenement_id = $_GET['id'];
                supprimerEvenement($evenement_id);
            }
            break;
        case 'valider':
            if (isset($_GET['id'])) {
                $evenement_id = $_GET['id'];
                validerEvenement($evenement_id);
            }
            break;
        // Ajoutez d'autres cas selon vos besoins
    }

    // Arrêtez l'exécution du script après avoir traité l'action
    exit();
}

// Reste du code PHP pour afficher les événements
afficherEvenements();

function afficherEvenements() {
    global $pdo;

    $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    // Vérifier si l'ID de l'utilisateur est défini
    if (!$userId) {
        // Rediriger ou afficher un message d'erreur, car l'utilisateur n'est pas connecté
        header("Location: ../connection.html");
        exit();
    }

    // Récupérer les photos depuis la base de données
    $query = $pdo->prepare("SELECT * FROM evenement WHERE ID_utilisateur = :userId ORDER BY Date_evenement DESC");
    $query->bindParam(':userId', $userId, PDO::PARAM_INT);
    $query->execute();

   

    echo "<table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Nom</th>
                    <th>Validé</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['Date_evenement'] . "</td>";
        echo "<td>" . $row['nom_evenement'] . "</td>";
        echo "<td>" . ($row['approuve'] ? 'Oui' : 'Non') . "</td>";
        echo "<td>
                <a href='?action=modifier&id=" . $row['ID_evenement'] . "'>Modifier</a> |
                <a href='?action=supprimer&id=" . $row['ID_evenement'] . "'>Supprimer</a> |
                
              </td>";
        echo "</tr>";
    }

    echo "</tbody>
        </table>";
}

function modifierEvenement($evenement_id) {
    global $pdo;

    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les valeurs soumises
        $nouveauNom = $_POST['nouveauNom'];
        $nouvelleDate = $_POST['nouvelleDate'];

        // Mettre à jour l'événement dans la base de données
        $query = $pdo->prepare("UPDATE evenement SET nom_evenement = :nom, Date_evenement = :date WHERE ID_evenement = :id");
        $query->bindParam(':nom', $nouveauNom, PDO::PARAM_STR);
        $query->bindParam(':date', $nouvelleDate, PDO::PARAM_STR);
        $query->bindParam(':id', $evenement_id, PDO::PARAM_INT);

        try {
            $query->execute();
            echo "L'événement a été modifié avec succès.";
            header("Location: ../dashboardUtilisateur.php");
        } catch (PDOException $e) {
            echo "Erreur lors de la modification de l'événement : " . $e->getMessage();
        }
    }

    // Récupérer les détails actuels de l'événement
    $query = $pdo->prepare("SELECT * FROM evenement WHERE ID_evenement = :id");
    $query->bindParam(':id', $evenement_id, PDO::PARAM_INT);
    $query->execute();
    $evenement = $query->fetch(PDO::FETCH_ASSOC);

    // Afficher le formulaire de modification avec les détails actuels
    echo "<h2>Modifier l'événement</h2>";
    echo "<form method='POST' action='?action=modifier&id={$evenement['ID_evenement']}'>";
    echo "<label for='nouveauNom'>Nouveau Nom:</label>";
    echo "<input type='text' name='nouveauNom' value='{$evenement['nom_evenement']}' required><br>";

    echo "<label for='nouvelleDate'>Nouvelle Date:</label>";
    echo "<input type='date' name='nouvelleDate' value='{$evenement['Date_evenement']}' required><br>";

    echo "<input type='submit' value='Enregistrer les modifications'>";
    echo "</form>";
}

function supprimerEvenement($evenement_id) {
    global $pdo;

    // Supprimer l'événement de la base de données
    $query = $pdo->prepare("DELETE FROM evenement WHERE ID_evenement = :id");
    $query->bindParam(':id', $evenement_id, PDO::PARAM_INT);

    try {
        $query->execute();
        echo "L'événement a été supprimé avec succès.";
        header("Location: ../dashboardUtilisateur.php");
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression de l'événement : " . $e->getMessage();
    }
}


?>
