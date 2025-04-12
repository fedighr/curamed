<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $entered_code = trim($_POST['verification_code_input']);

    if (isset($_SESSION['verification_code']) && $entered_code == $_SESSION['verification_code']) {

        $conn = mysqli_connect("localhost", "root", "", "curamed");
        if (!$conn) {
            die("Erreur lors de la connexion : " . mysqli_connect_error());
        }

        // Access session data
        $prenom   = mysqli_real_escape_string($conn, $_SESSION['registration_data']['prenom']);
        $nom      = mysqli_real_escape_string($conn, $_SESSION['registration_data']['nom']);
        $age      = (int) $_SESSION['registration_data']['age'];
        $email    = mysqli_real_escape_string($conn, $_SESSION['registration_data']['email']);
        $tel      = mysqli_real_escape_string($conn, $_SESSION['registration_data']['tel']);
        $mdp      = $_SESSION['registration_data']['mdp'];
        $userType = mysqli_real_escape_string($conn, $_SESSION['registration_data']['userType']);
        $userPhoto = mysqli_real_escape_string($conn, $_SESSION['registration_data']['photo_profil']);
        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

        // Insert into database
        $query = "INSERT INTO utilisateur (prenom, nom, age, email, mot_de_passe, telephone, type_utilisateur,photo_profil)
                  VALUES ('$prenom', '$nom', $age, '$email', '$mdp_hash', '$tel', '$userType','$userPhoto')";

        if (mysqli_query($conn, $query)) {
            echo "<script type='text/javascript'>";
            echo "alert('Compte créé avec succès!');";
            echo "window.location.href = 'information.html';";
            echo "</script>";
        } else {
            echo "<h2>Erreur lors de l'enregistrement : " . mysqli_error($conn) . "</h2>";
        }

        mysqli_close($conn);
        
        exit;
    } else {
        echo "<h2>Code de vérification invalide.</h2>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification</title>
    <link rel="stylesheet" href="styles/main.css">
    <style>
        .form-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }
        .form-container label {
            font-weight: bold;
        }
        .form-container input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 8px 0 16px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-container input[type="submit"] {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 14px 20px;
            margin: 8px 0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-container input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form method="post">
            <label for="verification_code_input">Entrez le code de vérification :</label>
            <input type="text" id="verification_code_input" name="verification_code_input" required>
            <input type="submit" value="Vérifier">
        </form>
    </div>
</body>
</html>
