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
            if (isset($_GET['type']) && isset($_GET['id'])) {
                $type = $_GET['type'];
                $id = $_GET['id'];
                modifierDoublon($type, $id);
            }
            break;
        case 'supprimer':
            if (isset($_GET['type']) && isset($_GET['id'])) {
                $type = $_GET['type'];
                $id = $_GET['id'];
                supprimerDoublon($type, $id);
            }
            break;
        // Ajoutez d'autres cas selon vos besoins
    }

    // Arrêtez l'exécution du script après avoir traité l'action
    exit();
}

// Reste du code PHP pour afficher les doublons
afficherDoublons();

function afficherDoublons() {
    global $pdo;

    // Rechercher les doublons de photos
    $doublonsPhotos = trouverDoublons('photos', 'titre');

    // Rechercher les doublons de membres
    $doublonsMembres = trouverDoublons('utilisateur', 'nom');

    // Rechercher les doublons d'événements
    $doublonsEvenements = trouverDoublons('evenement', 'nom_evenement');

    echo "<h1>Gestion des Doublons</h1>";

    // Afficher les doublons de photos
    afficherDoublonsType($doublonsPhotos, 'Photo');

    // Afficher les doublons de membres
    afficherDoublonsType($doublonsMembres, 'Membre');

    // Afficher les doublons d'événements
    afficherDoublonsType($doublonsEvenements, 'evenement');
}

function afficherDoublonsType($doublons, $type) {
    global $pdo;

    echo "<h2>Doublons de $type</h2>";

    if (empty($doublons)) {
        echo "Aucun doublon trouvé.";
        return;
    }

    echo "<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>";

    foreach ($doublons as $doublon) {
        echo "<tr>";
        echo "<td>" . $doublon['ID'] . "</td>";
        echo "<td>" . $doublon['Nom'] . "</td>";
        echo "<td>
                <a href='?action=modifier&type=$type&id=" . $doublon['ID'] . "'>Modifier</a> |
                <a href='?action=supprimer&type=$type&id=" . $doublon['ID'] . "'>Supprimer</a>
              </td>";
        echo "</tr>";
    }

    echo "</tbody>
        </table>";
}

function trouverDoublons($table, $colonne) {
    global $pdo;

    // Requête pour trouver les doublons
    $query = $pdo->query("SELECT $colonne, COUNT($colonne) AS occurrences FROM $table GROUP BY $colonne HAVING occurrences > 1");

    $doublons = [];

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        $doublons[] = [
            'Nom' => $row[$colonne],
            'Occurences' => $row['occurrences'],
            'ID' => obtenirIDDoublon($table, $colonne, $row[$colonne])
        ];
    }

    return $doublons;
}

function obtenirIDDoublon($table, $colonne, $valeur) {
    global $pdo;

    // Requête pour obtenir l'ID du doublon
    $query = $pdo->prepare("SELECT ID_utilisateur FROM $table WHERE $colonne = :valeur LIMIT 1");
    $query->bindParam(':valeur', $valeur, PDO::PARAM_STR);
    
    try {
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);

        // Retourner l'ID si trouvé, sinon NULL
        return ($result !== false) ? $result['ID_utilisateur'] : null;
    } catch (PDOException $e) {
        // Gérer les erreurs si nécessaire
        echo "Erreur lors de la récupération de l'ID du doublon : " . $e->getMessage();
        return null;
    }
}

function modifierDoublon($type, $id) {
    // Implémentez la logique pour modifier le doublon
    // Utilisez les fonctions et les formulaires nécessaires
    echo "Fonctionnalité de modification à implémenter pour $type avec l'ID $id.";
}

function supprimerDoublon($type, $id) {
    // Implémentez la logique pour supprimer le doublon
    // Utilisez les fonctions nécessaires
    echo "Fonctionnalité de suppression à implémenter pour $type avec l'ID $id.";
}
?>
