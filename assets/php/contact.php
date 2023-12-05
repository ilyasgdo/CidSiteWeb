<?php
session_start();

require_once('pdo.php');

// Traitement du formulaire de contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire de contact
    $email_contact = $_POST['email_contact'];
    $telephone_contact = $_POST['telephone_contact']; 
    $message_contact = $_POST['message_contact'];

    // Insérer les données dans la base de données
    try {
        $query = $pdo->prepare("INSERT INTO contact (email_envoyeur, Num_telephone_envoyeur, message) VALUES (:email, :telephone, :message)");
        $query->bindParam(':email', $email_contact, PDO::PARAM_STR);
        $query->bindParam(':telephone', $telephone_contact, PDO::PARAM_STR); 
        $query->bindParam(':message', $message_contact, PDO::PARAM_STR);
        $query->execute();

        echo "Le message a été enregistré avec succès dans la base de données.";
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

$user_name = "";
if (isset($_SESSION['user_email'])) {
    $user_name = $_SESSION['user_email'];
}
?>

<?php if ($user_name !== "") : ?>
        <p>Bienvenue, <?php echo $user_name; ?> !</p>
    <?php else : ?>
        <p>Vous n'êtes pas connecté.</p>
    <?php endif; ?>



<!-- Formulaire de contact -->
<h2>Contactez-nous</h2>
<form action="" method="post">
    <label for="email_contact">Votre email :</label>
    <input type="email" id="email_contact" name="email_contact" required><br>

    <label for="telephone_contact">Votre numéro de téléphone :</label>
    <input type="text" id="telephone_contact" name="telephone_contact" pattern="[0-9]{10,15}" required>
    <small>Format : 10 à 15 chiffres</small><br>

    <label for="message_contact">Votre message :</label>
    <textarea id="message_contact" name="message_contact" required></textarea><br>

    <input type="submit" value="Envoyer">
</form>
