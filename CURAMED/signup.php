<?php
session_start();
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $photo_name = $_FILES['photo']['name'];
    $photo_tmp = $_FILES['photo']['tmp_name'];
    $photo_folder = "user_photos/" . uniqid() . "_" . basename($photo_name);
    if(!move_uploaded_file($photo_tmp, $photo_folder)){
        echo "erreur lors de l'image";
        exit();
    }
    $_SESSION['registration_data'] = [
        'prenom'   => $_POST["prenom"],
        'nom'      => $_POST["nom"],
        'age'      => $_POST["age"],
        'email'    => $_POST["email"],
        'tel'      => $_POST["tel"],
        'mdp'      => $_POST["mdp"],
        'userType' => $_POST["userType"],
        'photo_profil' => $photo_folder,

    ];  
    
    $verification_code = rand(1000, 9999);
    $_SESSION['verification_code'] = $verification_code;


    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'topfadighribi11@gmail.com';
        $mail->Password   = 'skbqackvjratqaxn';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom('topfadighribi11@gmail.com', 'Curamed');
        $mail->addAddress($_SESSION['registration_data']['email'], $_SESSION['registration_data']['prenom'] . ' ' . $_SESSION['registration_data']['nom']);
        $mail->isHTML(true);
        $mail->Subject = 'Votre code de verification';
        $mail->Body    = 'Votre code de verification est : <b>' . $verification_code . '</b>';
        $mail->send();

        header("Location: verification.php");
        exit;
    } catch (Exception $e) {
        echo "L'email n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}";
    }
    exit;
}
?>