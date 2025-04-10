<?php
session_start();
header('Content-Type: application/json'); 

$conn = mysqli_connect("localhost", "root", "", "curamed");

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la connexion à la base de données.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = $_POST["mdp"]; 

    $sql = "SELECT * FROM utilisateur WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la préparation de la requête.']);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['user_email'] = $user['email'];

            // Send a successful response
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Aucun utilisateur trouvé avec cet e-mail.']);
    }

    mysqli_stmt_close($stmt); 
}

mysqli_close($conn);
?>
