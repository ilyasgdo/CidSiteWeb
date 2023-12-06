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
                $utilisateur_id = $_GET['id'];
                modifierUtilisateur($utilisateur_id);
            }
            break;
        case 'supprimer':
            if (isset($_GET['id'])) {
                $utilisateur_id = $_GET['id'];
                supprimerUtilisateur($utilisateur_id);
            }
            break;
        // Ajoutez d'autres cas selon vos besoins
    }

    // Arrêtez l'exécution du script après avoir traité l'action
    exit();
}

// Reste du code PHP pour afficher les utilisateurs
afficherUtilisateurs();

function afficherUtilisateurs() {
    global $pdo;

    // Récupérer les utilisateurs depuis la base de données
    $query = $pdo->query("SELECT * FROM utilisateur");

    echo "<h1>Gestion des Utilisateurs</h1>";
    echo "<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Parcours</th>
                    <th>Parcours Professionnel</th>
                    <th>Rôle</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['ID_utilisateur'] . "</td>";
        echo "<td>" . $row['nom'] . "</td>";
        echo "<td>" . $row['prenom'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "<td>" . $row['Parcours'] . "</td>";
        echo "<td>" . $row['Parcours_Professionnel'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "<td>
                <a href='?action=modifier&id=" . $row['ID_utilisateur'] . "'>Modifier</a> |
                <a href='?action=supprimer&id=" . $row['ID_utilisateur'] . "'>Supprimer</a>
              </td>";
        echo "</tr>";
    }

    echo "</tbody>
        </table>";
}

function modifierUtilisateur($utilisateur_id) {
    global $pdo;

    // Vérifier si le formulaire a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les valeurs soumises
        $nouveauNom = $_POST['nouveauNom'];
        $nouveauPrenom = $_POST['nouveauPrenom'];
        $nouveauEmail = $_POST['nouveauEmail'];
        $nouveauParcours = $_POST['nouveauParcours'];
        $nouveauParcoursProfessionnel = $_POST['nouveauParcoursProfessionnel'];
        $nouveauRole = $_POST['nouveauRole'];
        $nouveauMotDePasse = $_POST['nouveauMotDePasse'];

        // Mettre à jour l'utilisateur dans la base de données
        $query = $pdo->prepare("UPDATE utilisateur SET nom = :nom, prenom = :prenom, email = :email, Parcours = :parcours, Parcours_Professionnel = :parcours_professionnel, role = :role, password = :password WHERE ID_utilisateur = :id");
        $query->bindParam(':nom', $nouveauNom, PDO::PARAM_STR);
        $query->bindParam(':prenom', $nouveauPrenom, PDO::PARAM_STR);
        $query->bindParam(':email', $nouveauEmail, PDO::PARAM_STR);
        $query->bindParam(':parcours', $nouveauParcours, PDO::PARAM_STR);
        $query->bindParam(':parcours_professionnel', $nouveauParcoursProfessionnel, PDO::PARAM_STR);
        $query->bindParam(':role', $nouveauRole, PDO::PARAM_STR);
        $query->bindParam(':password', password_hash($nouveauMotDePasse, PASSWORD_DEFAULT), PDO::PARAM_STR);
        $query->bindParam(':id', $utilisateur_id, PDO::PARAM_INT);

        try {
            $query->execute();
            echo "L'utilisateur a été modifié avec succès.";
        } catch (PDOException $e) {
            echo "Erreur lors de la modification de l'utilisateur : " . $e->getMessage();
        }
    }

    // Récupérer les détails actuels de l'utilisateur
    $query = $pdo->prepare("SELECT * FROM utilisateur WHERE ID_utilisateur = :id");
    $query->bindParam(':id', $utilisateur_id, PDO::PARAM_INT);
    $query->execute();
    $utilisateur = $query->fetch(PDO::FETCH_ASSOC);

    // Afficher le formulaire de modification avec les détails actuels
    echo "<h2>Modifier l'utilisateur</h2>";
    echo "<form method='POST' action='?action=modifier&id={$utilisateur['ID_utilisateur']}'>";
    echo "<label for='nouveauNom'>Nouveau Nom:</label>";
    echo "<input type='text' name='nouveauNom' value='{$utilisateur['nom']}' required><br>";

    echo "<label for='nouveauPrenom'>Nouveau Prénom:</label>";
    echo "<input type='text' name='nouveauPrenom' value='{$utilisateur['prenom']}' required><br>";

    echo "<label for='nouveauEmail'>Nouveau Email:</label>";
    echo "<input type='email' name='nouveauEmail' value='{$utilisateur['email']}' required><br>";

    echo "<label for='nouveauParcours'>Nouveau Parcours:</label>";
    echo "<input type='text' name='nouveauParcours' value='{$utilisateur['Parcours']}' required><br>";

    echo "<label for='nouveauParcoursProfessionnel'>Nouveau Parcours Professionnel:</label>";
    echo "<input type='text' name='nouveauParcoursProfessionnel' value='{$utilisateur['Parcours_Professionnel']}'><br>";

    echo "<label for='nouveauRole'>Nouveau Rôle:</label>";
    echo "<select name='nouveauRole'>
            <option value='moderateur' " . ($utilisateur['role'] == 'moderateur' ? 'selected' : '') . ">Modérateur</option>
            <option value='user' " . ($utilisateur['role'] == 'user' ? 'selected' : '') . ">Utilisateur</option>
          </select><br>";

    echo "<label for='nouveauMotDePasse'>Nouveau Mot de passe:</label>";
    echo "<input type='password' name='nouveauMotDePasse' required><br>";

    echo "<input type='submit' value='Enregistrer les modifications'>";
    echo "</form>";
}

function supprimerUtilisateur($utilisateur_id) {
    global $pdo;

    // Supprimer l'utilisateur de la base de données
    $query = $pdo->prepare("DELETE FROM utilisateur WHERE ID_utilisateur = :id");
    $query->bindParam(':id', $utilisateur_id, PDO::PARAM_INT);

    try {
        $query->execute();
        echo "L'utilisateur a été supprimé avec succès.";
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage();
    }
}
?>
