<?php
session_start();

$conn=mysqli_connect("localhost","root","","curamed");

if(isset($_POST['delete_account'])) {
    $user_id = $_SESSION['user_id'];
    $user_type = $_SESSION['type'];

    if($user_type == 'patient') {
        mysqli_query($conn, "DELETE FROM patient WHERE id_patient = $user_id");
    } else {
        mysqli_query($conn, "DELETE FROM medecin WHERE id_medecin = $user_id");
    }

    mysqli_query($conn, "DELETE FROM utilisateur WHERE id_utilisateur = $user_id");

    session_destroy();
    
    header("Location: home.php");
    exit();
}
?><?php
session_start();
require 'conn.php';

if(isset($_POST['delete_account'])) {
    $user_id = $_SESSION['user_id'];
    $user_type = $_SESSION['type'];

    try {
        mysqli_begin_transaction($conn);

        // Delete from child tables first
        if($user_type == 'patient') {
            mysqli_query($conn, "DELETE FROM patient WHERE id_patient = $user_id");
        } else {
            mysqli_query($conn, "DELETE FROM medecin WHERE id_medecin = $user_id");
        }

        // Delete from utilisateur
        mysqli_query($conn, "DELETE FROM utilisateur WHERE id_utilisateur = $user_id");

        mysqli_commit($conn);
        
        session_destroy();
        header("Location: home.php");
        exit();

    } catch (Exception $e) {
        mysqli_rollback($conn);
        die("Error deleting account: " . $e->getMessage());
    }
}
?>