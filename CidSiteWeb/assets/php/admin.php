<?php
$motDePasse = 'ilyasgdo';

if (isset($_POST['motdepasse']) && $_POST['motdepasse'] === $motDePasse) {
    require('./pdo.php');

    $size = $_GET['max'] ?? 20;
    $start = $_GET['min'] ?? 0;

    $size = max(1, min(25, $size));
    $start = max(0, $start);

    $query = "SELECT id, nom, email, telephone, message
              FROM formulaire
              ORDER BY id ASC
              LIMIT :start, :size";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':size', $size, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($result) > 0) {
        echo '<table border="1">
                <tr>
                    <th>id</th>
                    <th>nom</th>
                    <th>email</th>
                    <th>telephone</th>
                    <th>message</th>
                </tr>';

        foreach ($result as $row) {
            echo '<tr>
                    <td>' . $row['id'] . '</td>
                    <td>' . $row['nom'] . '</td>
                    <td>' . $row['email'] . '</td>
                    <td>' . $row['telephone'] . '</td>
                    <td>' . $row['message'] . '</td>
                </tr>';
        }

        echo '</table>';
    } else {
        echo 'pas de résultat';
    }

    
    echo '<div class="container mt-3">';
    if ($start > 0) {
        $reculeStart = max(0, $start - $size);
        echo '<a href="liste.php?min=' . $reculeStart . '&max=' . $size . '" class="btn btn-primary">Reculer de 20</a>';

        $reculeStart100 = max(0, $start - 100);
        echo '<a href="liste.php?min=' . $reculeStart100 . '&max=' . $size . '" class="btn btn-primary ml-2">Reculer de 100</a>';
    }

    if (count($result) == $size) {
        $nextStart = $start + $size;
        echo '<a href="liste.php?min=' . $nextStart . '&max=' . $size . '" class="btn btn-primary ml-2">Avancer de 20</a>';

        $nextStart100 = $start + 100;
        echo '<a href="liste.php?min=' . $nextStart100 . '&max=' . $size . '" class="btn btn-primary ml-2">Avancer de 100</a>';
    }

    echo '</div>';
} else {
    // Si le mot de passe n'est pas correct, affiche le formulaire de mot de passe
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Page d'administration - Authentification</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    </head>
    <body>
        <div class="container mt-5">
            <h2 class="mb-3">Authentification requise</h2>
            <form method="post">
                <div class="mb-3">
                    <label for="motdepasse" class="form-label">Entrez le mot de passe :</label>
                    <input type="password" name="motdepasse" id="motdepasse" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Se connecter</button>
            </form>
        </div>
    </body>
    </html>
    <?php
}
?>
