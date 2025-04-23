<?php 
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", "", "curamed");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $id_user = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';

    if (isset($_POST['profile'])) {
        $user_id = intval($_POST['user_id']);
        $sql = "SELECT 1 from utilisateur where type_utilisateur='patient' and id_utilisateur=".$user_id;
        $res=mysqli_query($conn,$sql);
        if(mysqli_num_rows($res)>0){
            header("Location: profile_p.php?id=" . $user_id);
        }
        else{
            header("Location: profile_d.php?id=" . $user_id);
        }
        
        exit();
    }

    if (isset($_POST['modifier'])) {
        $userId = intval($_POST['user_id']);
        $userType = $_POST['user_type'];
    
        $nom = mysqli_real_escape_string($conn, $_POST['nom']);
        $prenom = mysqli_real_escape_string($conn, $_POST['prenom']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $telephone = mysqli_real_escape_string($conn, $_POST['telephone']);


    $updateFields = [
        "nom = '$nom'",
        "prenom = '$prenom'",
        "email = '$email'",
        "telephone = '$telephone'"
    ];

    if (!empty($_POST['password'])) {
        if ($_POST['password'] === $_POST['confirm_password']) {
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $updateFields[] = "mot_de_passe = '$hashedPassword'";
        } else {
            $_SESSION['error'] = "Les mots de passe ne correspondent pas";
            header('Location: dashboard.php');
            exit();
        }
    }


    if (!empty($_FILES['photo']['name'])) {
        $photo_name = $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];
        $photo_folder = "user_photos/" . uniqid() . "_" . basename($photo_name);
        
        if (move_uploaded_file($photo_tmp, $photo_folder)) {

            $sql = "SELECT photo_profil FROM utilisateur WHERE id_utilisateur = $userId";
            $result = mysqli_query($conn, $sql);
            $oldPhoto = mysqli_fetch_assoc($result)['photo_profil'];
            if (file_exists($oldPhoto)) {
                unlink($oldPhoto);
            }
            $updateFields[] = "photo_profil = '$photo_folder'";
        }
    }


    $sql = "UPDATE utilisateur SET " . implode(', ', $updateFields) . " WHERE id_utilisateur = $userId";
    
    if (mysqli_query($conn, $sql)) {

        if ($userType === 'medecin') {
            $specialite = mysqli_real_escape_string($conn, $_POST['specialite']);
            $sql = "UPDATE medecin SET specialite = '$specialite' WHERE id_medecin = $userId";
            mysqli_query($conn, $sql);
        }
        
        $_SESSION['message'] = "Utilisateur mis à jour avec succès!";
    } else {
        $_SESSION['error'] = "Erreur de mise à jour: " . mysqli_error($conn);
    }
    header('Location: dashboard.php');
    exit();
    }

    if (isset($_POST['admin'])) {
        $req = "UPDATE utilisateur SET role='admin' WHERE id_utilisateur=" . $id_user;
        if (!mysqli_query($conn, $req)) {
            echo "Erreur admin: " . mysqli_error($conn);
            exit();
        }
        $_SESSION['message'] = "Rôle admin ajouté avec succès!";
        header('Location: dashboard.php');
        exit();
    }

    if (isset($_POST['nadmin'])) {
        $req = "UPDATE utilisateur SET role='regular' WHERE id_utilisateur=" . $id_user;
        if (!mysqli_query($conn, $req)) {
            echo "Erreur non admin: " . mysqli_error($conn);
            exit();
        }
        $_SESSION['message'] = "Rôle admin retiré avec succès!";
        header('Location: dashboard.php');
        exit();
    }

    if (isset($_POST['supp'])) {
        $id_user = intval($_POST['user_id']);

        $sql = "SELECT photo_profil FROM utilisateur WHERE id_utilisateur = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_user);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $photo);
            mysqli_stmt_fetch($stmt);

            if (file_exists($photo)) {
                if (!unlink($photo)) {
                    echo "Erreur lors de la suppression de la photo.";
                }
            }
        } else {
            echo "Query failed.";
        }
        
        mysqli_stmt_close($stmt);

        $sql = "DELETE FROM utilisateur WHERE id_utilisateur = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_user);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "User deleted successfully!";
        } else {
            $_SESSION['message'] = "Error: " . mysqli_error($conn);
        }
        if($id_user === $_SESSION['user_id']){
            session_unset();
            session_destroy();
            header('Location: home.php');
        }
        else{
            header('Location: dashboard.php');
        }
        mysqli_stmt_close($stmt);
        exit();
    }

}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['nom']) && isset($_POST['prenom'])) {
        $userType = $_POST['user_type'];
        
        // Handle file upload
        $photo_name = $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];
        $photo_folder = "user_photos/" . uniqid() . "_" . basename($photo_name);
        if (!move_uploaded_file($photo_tmp, $photo_folder)) {
            die("Erreur lors du téléchargement de l'image.");
        }

        // Insert into utilisateur
        $sql = "INSERT INTO utilisateur (nom, prenom, email, telephone, mot_de_passe, role, type_utilisateur, photo_profil)
                VALUES (?, ?, ?, ?, ?, 'regular', ?, ?)";
        
        $stmt = mysqli_prepare($conn, $sql);
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        mysqli_stmt_bind_param($stmt, "sssssss", 
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['email'],
            $_POST['telephone'],
            $hashedPassword,
            $userType,
            $photo_folder
        );

        if (mysqli_stmt_execute($stmt)) {
            $newUserId = mysqli_insert_id($conn);
            
            if ($userType === 'medecin') {
                $sqlMedecin = "INSERT INTO medecin (id_medecin, specialite)
                               VALUES (?, ?)";
                $stmtMedecin = mysqli_prepare($conn, $sqlMedecin);
                mysqli_stmt_bind_param($stmtMedecin, "is", $newUserId, $_POST['specialite']);
                mysqli_stmt_execute($stmtMedecin);
            }
            
            $_SESSION['message'] = "Utilisateur créé avec succès!";
        } else {
            $_SESSION['message'] = "Erreur: " . mysqli_error($conn);
        }
        
        header('Location: dashboard.php');
        exit();
    }
}
mysqli_close($conn);
?>