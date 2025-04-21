<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", "", "curamed");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }


    if (isset($_POST['profile'])) {
        $user_id = intval($_POST['user_id']);
        header("Location: profile.php?id=" . $user_id);
        exit();
    }


    if (isset($_POST['modifier'])) {
        $id_user = intval($_POST['user_id']);
        $user_type = $_POST['user_type'] ?? '';


        $nom = mysqli_real_escape_string($conn, $_POST['nom']);
        $prenom = mysqli_real_escape_string($conn, $_POST['prenom']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $telephone = mysqli_real_escape_string($conn, $_POST['telephone']);

        $sqlCheck = "SELECT email FROM utilisateur WHERE id_utilisateur = ?";
        $stmtCheck = mysqli_prepare($conn, $sqlCheck);
        mysqli_stmt_bind_param($stmtCheck, "i", $id_user);
        mysqli_stmt_execute($stmtCheck);
        $result = mysqli_stmt_get_result($stmtCheck);
        $currentUser = $result->fetch_assoc();

 
        if ($currentUser['email'] !== $email) {
            $sqlCheckEmail = "SELECT id_utilisateur FROM utilisateur WHERE email = ?";
            $stmtCheckEmail = mysqli_prepare($conn, $sqlCheckEmail);
            mysqli_stmt_bind_param($stmtCheckEmail, "s", $email);
            mysqli_stmt_execute($stmtCheckEmail);
            $emailResult = mysqli_stmt_get_result($stmtCheckEmail);
            
            if ($emailResult->num_rows > 0) {
                $_SESSION['error'] = "Cet email est déjà utilisé par un autre utilisateur!";
                header('Location: dashboard.php');
                exit();
            }
        }


        $sql = "UPDATE utilisateur SET 
                nom = ?, 
                prenom = ?, 
                email = ?, 
                telephone = ? 
                WHERE id_utilisateur = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssi", $nom, $prenom, $email, $telephone, $id_user);
        
        if (mysqli_stmt_execute($stmt)) {
            if ($user_type === 'medecin') {
                $specialite = mysqli_real_escape_string($conn, $_POST['specialite']);
                $sqlMedecin = "UPDATE medecin SET specialite = ? WHERE id_medecin = ?";
                $stmtMedecin = mysqli_prepare($conn, $sqlMedecin);
                mysqli_stmt_bind_param($stmtMedecin, "si", $specialite, $id_user);
                mysqli_stmt_execute($stmtMedecin);
            }
            $_SESSION['message'] = "Utilisateur mis à jour avec succès!";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour: " . mysqli_error($conn);
        }
        header('Location: dashboard.php');
        exit();
    }

    // Handle User Creation
    if (isset($_POST['nom']) && isset($_POST['prenom'])) {
        $userType = $_POST['user_type'];
        $photo_name = $_FILES['photo']['name'];
        $photo_tmp = $_FILES['photo']['tmp_name'];
        $photo_folder = "user_photos/" . uniqid() . "_" . basename($photo_name);

        if (!move_uploaded_file($photo_tmp, $photo_folder)) {
            die("Erreur lors du téléchargement de l'image.");
        }

        // Insert user
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
            $_SESSION['error'] = "Erreur: " . mysqli_error($conn);
        }
        header('Location: dashboard.php');
        exit();
    }

    //changement admin
    if (isset($_POST['admin'])) {
        $id_user = intval($_POST['user_id']);
        $req = "UPDATE utilisateur SET role='admin' WHERE id_utilisateur=" . $id_user;
        mysqli_query($conn, $req);
        $_SESSION['message'] = "Rôle admin ajouté avec succès!";
        header('Location: dashboard.php');
        exit();
    }

    if (isset($_POST['nadmin'])) {
        $id_user = intval($_POST['user_id']);
        $req = "UPDATE utilisateur SET role='regular' WHERE id_utilisateur=" . $id_user;
        mysqli_query($conn, $req);
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
            header('Location: index.php');
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