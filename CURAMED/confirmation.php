<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", "", "curamed");
    
    if (!$conn) {
        $_SESSION['error'] = "Connection failed: " . mysqli_connect_error();
        header("Location: home.php");
        exit();
    }

    mysqli_begin_transaction($conn);

    try {
        // Validate inputs
        if (!isset($_POST['id_rdv']) || !is_numeric($_POST['id_rdv'])) {
            throw new Exception("Invalid appointment ID");
        }

        $id_rdv = intval($_POST['id_rdv']);
        $doctor_id = intval($_SESSION['user_id']);

        // Verify the appointment exists and get patient ID
        $stmt = mysqli_prepare($conn, 
            "SELECT id_patient, id_medecin, date_heure 
            FROM rendez_vous 
            WHERE id_rdv = ?");
        mysqli_stmt_bind_param($stmt, "i", $id_rdv);
        mysqli_stmt_execute($stmt);
        $rdv = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$rdv) {
            throw new Exception("Appointment not found");
        }

        // Verify the logged-in doctor owns this appointment
        if ($rdv['id_medecin'] != $doctor_id) {
            throw new Exception("You don't have permission for this appointment");
        }

        // Get patient's user_id from patient table
        $stmt = mysqli_prepare($conn, 
            "SELECT id_utilisateur FROM patient 
            WHERE id_patient = ?");
        mysqli_stmt_bind_param($stmt, "i", $rdv['id_patient']);
        mysqli_stmt_execute($stmt);
        $patient_user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        
        if (!$patient_user) {
            throw new Exception("Patient user not found");
        }
        $patient_user_id = $patient_user['id_utilisateur'];

        // Handle accept/refuse actions
        if (isset($_POST['accepter'])) {
            // Update appointment status
            $stmt = mysqli_prepare($conn, 
                "UPDATE rendez_vous SET statut = 'confirmé' 
                WHERE id_rdv = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_rdv);
            mysqli_stmt_execute($stmt);

            // Update payment status
            $stmt = mysqli_prepare($conn, 
                "UPDATE paiement SET statut = 'payé' 
                WHERE id_rdv = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_rdv);
            mysqli_stmt_execute($stmt);

            // Notification message
            $message = "Rendez-vous confirmé pour le " . 
                date('d/m/Y H:i', strtotime($rdv['date_heure'])) . 
                " avec Dr. " . $_SESSION['prenom'] . " " . $_SESSION['nom'];

        } elseif (isset($_POST['refuser'])) {
            // Update appointment status
            $stmt = mysqli_prepare($conn, 
                "UPDATE rendez_vous SET statut = 'annulé' 
                WHERE id_rdv = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_rdv);
            mysqli_stmt_execute($stmt);

            // Delete payment
            $stmt = mysqli_prepare($conn, 
                "DELETE FROM paiement WHERE id_rdv = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_rdv);
            mysqli_stmt_execute($stmt);

            // Notification message
            $message = "Rendez-vous annulé pour le " . 
                date('d/m/Y H:i', strtotime($rdv['date_heure'])) . 
                " avec Dr. " . $_SESSION['prenom'] . " " . $_SESSION['nom'];
        } else {
            throw new Exception("No action specified");
        }

        // Create new notification for patient
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO notification 
            (id_utilisateur, message, date_envoi, type, statut, id_rdv, id_patient)
            VALUES (?, ?, NOW(), 'email', 'non_lu', ?, ?)");
        mysqli_stmt_bind_param($stmt, "isii", 
            $patient_user_id, 
            $message, 
            $id_rdv, 
            $rdv['id_patient']
        );
        mysqli_stmt_execute($stmt);

        // Delete doctor's notification
        $stmt = mysqli_prepare($conn, 
            "DELETE FROM notification 
            WHERE id_rdv = ? AND id_utilisateur = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id_rdv, $doctor_id);
        mysqli_stmt_execute($stmt);

        mysqli_commit($conn);
        $_SESSION['success'] = "Action completed successfully";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['error'] = $e->getMessage();
    } finally {
        mysqli_close($conn);
        header("Location: home.php");
        exit();
    }
}