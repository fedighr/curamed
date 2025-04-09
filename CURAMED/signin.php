<?php
$conn = mysqli_connect("localhost", "root", "", "curamed");

if (!$conn) {
    echo "Erreur lors de la connexion à la base de données.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM utilisateurs WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['mot_de_passe'])) {
            session_start();
            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['user_email'] = $user['email'];
            
            header("Location: index.html");
            exit();
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Aucun utilisateur trouvé avec cet e-mail.";
    }
}

mysqli_close($conn);
?>
