<?php
$base = array(
    "nombase" => "testsite",
    "adrServ" => "localhost",
    "port" => "3306",
    "utilisateur" => "root",
    "mdp" => "",
    "charset" => "utf8mb4"
);

try {
    $pdo = new PDO(
        "mysql:host={$base['adrServ']};port={$base['port']};dbname={$base['nombase']};charset={$base['charset']}",
        $base['utilisateur'],
        $base['mdp']
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();

}
?>
