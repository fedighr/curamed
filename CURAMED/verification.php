<?php
require 'vendor/autoload.php';  // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = mysqli_connect("localhost", "root", "", "curamed");

if (!$conn) {
    echo("Erreur lors de la connexion");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $code = $_POST['verification_code'];

    // Retrieve the stored verification code from the database
    $query = "SELECT verification_code FROM utilisateur WHERE email = '$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $stored_code = $row['verification_code'];

        // Check if the entered verification code matches the one stored in the database
        if ($code == $stored_code) {
            // If codes match, mark the account as verified
            $update_query = "UPDATE utilisateur SET is_verified = 1 WHERE email = '$email'";
            if (mysqli_query($conn, $update_query)) {
                echo "Votre compte a été vérifié avec succès.";
            } else {
                echo "Erreur lors de la vérification.";
            }
        } else {
            echo "Code de vérification invalide.";
        }
    } else {
        echo "Aucun utilisateur trouvé avec cet email.";
    }
}

mysqli_close($conn);
?>
