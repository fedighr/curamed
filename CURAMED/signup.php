<?php
require 'vendor/autoload.php';  // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = mysqli_connect("localhost", "root", "", "curamed");
if (!$conn) {
    echo("Erreur lors de la connexion");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = $_POST["prenom"];
    $nom = $_POST["nom"];
    $age = (int)$_POST["age"];
    $email = $_POST["email"];
    $tel = $_POST["tel"];
    $mdp = $_POST["mdp"];
    $type = $_POST["userType"];
}

$mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

$req = "INSERT INTO utilisateur (prenom, nom, age, email, mot_de_passe, telephone, type_utilisateur) 
        VALUES ('$prenom', '$nom', $age, '$email', '$mdp_hash', '$tel', '$type')";

if (mysqli_query($conn, $req)) {
    $verification_code = rand(1000, 9999);

    // Send verification email with PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'topfadighribi11@gmail.com';  // Your Gmail address
        $mail->Password = 'skbqackvjratqaxn';  // Your Gmail password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;  // Set the SMTP port

        // Recipients
        $mail->setFrom('fadiyghribie@gmail.com', 'Mailer');
        $mail->addAddress($email, $prenom . ' ' . $nom);  // Send to the user's email

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Code de Vérification';
        $mail->Body    = 'Voici votre code de vérification: <b>' . $verification_code . '</b>';

        $mail->send();

        // Store verification code in the database (assuming you have a `verification_code` column)
        $updateQuery = "UPDATE utilisateur SET token = '$verification_code' WHERE email = '$email'";
        mysqli_query($conn, $updateQuery);

        echo 'Inscription réussie ! Un code de vérification a été envoyé à votre email.';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    echo "Inscription échouée : " . mysqli_error($conn);
}

mysqli_close($conn);
?>
