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
        // Ajoutez d'autres cas selon vos besoins
    }

    // Arrêtez l'exécution du script après avoir traité l'action
    exit();
}

// Reste du code PHP pour afficher les contacts
afficherContacts();

function afficherContacts() {
    global $pdo;

    // Récupérer les contacts depuis la base de données
    $query = $pdo->query("SELECT * FROM Contact");

    echo "<h1>Liste des Contacts</h1>";
    echo "<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email Envoyeur</th>
                    <th>Numéro de Téléphone Envoyeur</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>";

    while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['Id_message'] . "</td>";
        echo "<td>" . $row['email_envoyeur'] . "</td>";
        echo "<td>" . $row['Num_telephone_envoyeur'] . "</td>";
        echo "<td>" . $row['message'] . "</td>";
        echo "</tr>";
    }

    echo "</tbody>
        </table>";
}
?>
