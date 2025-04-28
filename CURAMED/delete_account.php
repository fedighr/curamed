<?php
session_start();
$conn=mysqli_connect("localhost","root","","curamed");

    $user_id = $_SESSION['user_id'];
    $user_type = $_SESSION['type'];

    try {
        $conn->begin_transaction();

        if (file_exists($_SESSION['photo'])) {
            if (!unlink($_SESSION['photo'])) {
                echo "Erreur lors de la suppression de la photo.";
            }
        }

        $conn->query("DELETE FROM utilisateur WHERE id_utilisateur = $user_id");

        $conn->commit();
        
        session_destroy();
        header("Location: home.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Error deleting account: " . $e->getMessage());
    }
?>