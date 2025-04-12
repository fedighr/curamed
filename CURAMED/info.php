<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_SESSION['registration_data']) && isset($_SESSION['registration_data']['email'])) {

        if (isset($_SESSION['registration_data'])) {
            $conn = mysqli_connect("localhost", "root", "", "curamed");

            if (!$conn) {
                echo "Erreur de connexion";
                exit;
            }


            $email = mysqli_real_escape_string($conn, $_SESSION['registration_data']['email']);

            $query = "SELECT id_utilisateur,type_utilisateur FROM utilisateur WHERE email='$email'";
            $result = mysqli_query($conn, $query);

            if ($row = mysqli_fetch_assoc($result)) {
                $id = $row['id_utilisateur'];
                $type = $row['type_utilisateur'];
                if($type == 'patient'){

                    $taille = $_POST["taille"];
                    $poids = $_POST["poids"];
                    $mc = $_POST["maladies_chroniques"];
                    $g_s = $_POST["group_sanguin"];
                    $info = $_POST["info"];

                    $query = "INSERT INTO patient 
                          VALUES ('$id', '$taille', '$poids', '$mc', '$g_s', '$info')";
                }
                else{
                    $spec = $_POST["specialite"];
                    $adresse = $_POST["adresse"];
                    $experience = $POST["experience"];
                    $query = "INSERT INTO medecin 
                          VALUES ('$id', '$spec', '$adresse', '$experience')";

                }

                if (mysqli_query($conn, $query)) {
                    echo "<script type='text/javascript'>";
                    echo "alert('Compte créé avec succès!');";
                    echo "window.location.href = 'login.html';";
                    echo "</script>";
                } else {
                    echo "<h2>Erreur lors de l'enregistrement : " . mysqli_error($conn) . "</h2>";
                }
            } else {
                echo "Utilisateur non trouvé";
            }

            mysqli_close($conn);
        } else {
            echo "Données d'enregistrement non trouvées dans la session.";
        }
    }
    unset($_SESSION['registration_data']);
    unset($_SESSION['verification_code']);
}
?>
