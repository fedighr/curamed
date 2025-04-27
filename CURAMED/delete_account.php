<?php
session_start();
$conn=mysqli_connect("localhost","root","","curamed");

    $user_id = $_SESSION['user_id'];
    $user_type = $_SESSION['type'];

    try {
        $conn->begin_transaction();

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