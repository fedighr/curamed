<?php
session_start();
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check_email']) && $_POST['check_email'] == "1") {
    header('Content-Type: application/json');
    $conn = mysqli_connect("localhost", "root", "", "curamed");
    if (!$conn) {
        echo json_encode(['result' => false, 'message' => 'Erreur de connexion à la base de données.']);
        exit();
    }

    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $stmt = mysqli_prepare($conn, "SELECT id_utilisateur FROM utilisateur WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    
    echo json_encode(['result' => mysqli_num_rows($res) === 0]);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", "", "curamed");
    if (!$conn) {
        die("Erreur de connexion à la base de données.");
    }


    if (empty($_FILES['photo']['name'])) {
        die("Veuillez télécharger une photo.");
    }
    $photo_name = $_FILES['photo']['name'];
    $photo_tmp = $_FILES['photo']['tmp_name'];
    $photo_folder = "user_photos/" . uniqid() . "_" . basename($photo_name);
    if (!move_uploaded_file($photo_tmp, $photo_folder)) {
        die("Erreur lors du téléchargement de l'image.");
    }


    $_SESSION['registration_data'] = [
        'prenom'        => $_POST["prenom"],
        'nom'           => $_POST["nom"],
        'age'           => $_POST["age"],
        'email'         => $_POST["email"],
        'tel'           => $_POST["tel"],
        'mdp'           => $_POST["mdp"],
        'userType'      => $_POST["userType"],
        'genre'         =>$_POST['sexe'],
        'photo_profil'  => $photo_folder,
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
        $mail->addAddress($_POST["email"], $_POST["prenom"] . ' ' . $_POST["nom"]);
        $mail->isHTML(true);
        $mail->Subject = 'Votre code de vérification';
        $mail->Body    = 'Votre code de vérification est : <b>' . $verification_code . '</b>';
        $mail->send();


        header("Location: verification.php");
        exit();
    } catch (Exception $e) {
        die("L'email n'a pas pu être envoyé. Erreur: " . $mail->ErrorInfo);
    }

    mysqli_close($conn);
} else {

    die("Méthode non autorisée.");
}
?>