<?php
ob_start(); // Start output buffering
session_start();
header('Content-Type: application/json');
error_reporting(0);
ini_set('display_errors', 0);

$conn = mysqli_connect("localhost","root","","curamed");

$response = ['success' => false, 'message' => ''];

try {
    if($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode non autorisée');
    }
    
    if(!isset($_SESSION['user_id'])) {
        throw new Exception('Session expirée, veuillez vous reconnecter');
    }


    $required = ['prenom', 'nom', 'age', 'genre'];
    foreach($required as $field) {
        if(!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            throw new Exception("Le champ $field est requis");
        }
    }

    $user_id = $_SESSION['user_id'];
    $prenom = mysqli_real_escape_string($conn, $_POST['prenom']);
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);
    $age = intval($_POST['age']);
    $genre = mysqli_real_escape_string($conn, $_POST['genre']);

    // Validate age
    if($age < 0 || $age > 150) {
        throw new Exception("Âge invalide");
    }

    $sql = "UPDATE utilisateur SET 
            prenom = '$prenom',
            nom = '$nom',
            age = $age,
            genre = '$genre'
            WHERE id_utilisateur = $user_id";

    if(!mysqli_query($conn, $sql)) {
        throw new Exception("Erreur de base de données: " . mysqli_error($conn));
    }

    $response['success'] = true;
    $response['message'] = 'Profil mis à jour avec succès';

} catch(Exception $e) {
    $response['message'] = $e->getMessage();
}

ob_end_clean();
echo json_encode($response);
exit();
?>