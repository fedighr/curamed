<?php
// confirmation.php
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
        // Vérifier si id_rdv est passé dans POST et est valide
        if (!isset($_POST['id_rdv']) || !is_numeric($_POST['id_rdv'])) {
            throw new Exception("Invalid appointment ID");
        }

        $id_rdv = intval($_POST['id_rdv']);
        $user_id = intval($_SESSION['user_id']);

        // Vérification si la notification existe pour cet utilisateur
        $stmt = mysqli_prepare($conn, "SELECT id_patient FROM notification WHERE id_rdv = ? AND id_utilisateur = ?");
        mysqli_stmt_bind_param($stmt, "ii", $id_rdv, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $notification = mysqli_fetch_assoc($result);

        if (!$notification) {
            throw new Exception("Notification not found");
        }

        // Si l'utilisateur accepte le rendez-vous
        if (isset($_POST['accepter'])) {
            $stmt = mysqli_prepare($conn, "UPDATE rendez_vous SET statut = 'confirmé' WHERE id_rdv = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_rdv);
            mysqli_stmt_execute($stmt);

            // Mise à jour du paiement
            $stmt = mysqli_prepare($conn, "UPDATE paiement SET statut = 'payé' WHERE id_rdv = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_rdv);
            mysqli_stmt_execute($stmt);

            // Message de confirmation
            if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])) {
                $message = "Rendez-vous confirmé avec Dr. " . $_SESSION['prenom'] . " " . $_SESSION['nom'];
            } else {
                throw new Exception("Nom ou prénom du médecin manquant");
            }

        // Si l'utilisateur refuse le rendez-vous
        } elseif (isset($_POST['refuser'])) {
            $stmt = mysqli_prepare($conn, "UPDATE rendez_vous SET statut = 'annulé' WHERE id_rdv = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_rdv);
            mysqli_stmt_execute($stmt);

            // Suppression du paiement
            $stmt = mysqli_prepare($conn, "DELETE FROM paiement WHERE id_rdv = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_rdv);
            mysqli_stmt_execute($stmt);

            // Message de refus
            if (isset($_SESSION['prenom']) && isset($_SESSION['nom'])) {
                $message = "Rendez-vous refusé avec Dr. " . $_SESSION['prenom'] . " " . $_SESSION['nom'];
            } else {
                throw new Exception("Nom ou prénom du médecin manquant");
            }
        } else {
            throw new Exception("Aucune action spécifiée");
        }

        // Mise à jour de la notification avec le message correspondant
        $stmt = mysqli_prepare($conn, "UPDATE notification SET message = ? WHERE id_rdv = ?");
        mysqli_stmt_bind_param($stmt, "si", $message, $id_rdv);
        mysqli_stmt_execute($stmt);

        // Suppression de la notification une fois l'action effectuée
        $stmt = mysqli_prepare($conn, "DELETE FROM notification WHERE id_rdv = ?");
        mysqli_stmt_bind_param($stmt, "i", $id_rdv);
        mysqli_stmt_execute($stmt);

        // Commit de la transaction
        mysqli_commit($conn);

        // Message de succès
        $_SESSION['success'] = "Opération réussie";
    } catch (Exception $e) {
        // Rollback en cas d'erreur
        mysqli_rollback($conn);
        $_SESSION['error'] = $e->getMessage();
    } finally {
        // Fermeture de la connexion
        mysqli_close($conn);
        // Redirection vers la page d'accueil
        header("Location: home.php");
        exit();
    }
}
