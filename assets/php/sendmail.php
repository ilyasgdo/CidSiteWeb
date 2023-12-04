<?php

require('./pdo.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    
    $stmt = $pdo->prepare("INSERT IGNORE INTO formulaire (nom, email, telephone, message) VALUES (:name, :email, :phone, :message)");

    $stmt->execute(array(
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':message' => $message
    ));

   
    header('Location: ../../index.html');

    exit; 
}
?>
