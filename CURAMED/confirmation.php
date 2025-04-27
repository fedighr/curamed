<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", "", "curamed");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    $sql = "SELECT id_patient from notification where id_utilisateur=".$_SESSION['user_id'];
    $res=mysqli_query($conn,$sql);
    $row=mysqli_fetch_assoc($res);
    if(isset($_POST['accepter'])){
        $req="UPDATE rendez_vous set statut='confirmé' where id_patient=".$row['id_patient']." and id_medecin=".$_SESSION['user_id'];
        $res=mysqli_query($conn,$req);
        $req="SELECT id_rdv from rendez_vous where id_patient=".$row['id_patient']." and id_medecin= ".$_SESSION['user_id'];
        $res=mysqli_query($conn,$req);
        $row2=mysqli_fetch_assoc($res);
        $req="UPDATE paiement set statut = 'payé' where id_patient=" .$row['id_patient']." and id_rdv=".$row2['id_rdv'];
        if(mysqli_query($conn,$req)){
            $nom_doctor=$_SESSION['prenom']." ".$_SESSION['nom'];
            $message = "rendez vous accepter avec docteur " . $nom_doctor;
            $req = "UPDATE notification SET message = '$message' WHERE id_utilisateur = " . $row['id_patient'];

            $res=mysqli_query($conn,$req);
            $sql =("
            SELECT u.*, p.* 
            FROM utilisateur u
            JOIN patient p ON u.id_utilisateur = p.id_patient
            WHERE u.id_utilisateur =
        ".$row['id_patient']);
        
        $res=mysqli_query($conn,$sql);
        
        if(!$patient=mysqli_fetch_assoc($res)) {
            die("Erreur de requête: " . $stmt->error);
        }
            $mpdf = new \Mpdf\Mpdf([
                'tempDir' => __DIR__ . '/pdf',
                'format' => 'A4',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 25,
                'margin_bottom' => 20,
                'margin_header' => 10,
                'margin_footer' => 10
            ]);
            $styles = '
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        line-height: 1.6;
                        color: #333;
                        margin: 0;
                        padding: 20px;
                    }

                    .header {
                        text-align: center;
                        margin-bottom: 30px;
                        padding-bottom: 20px;
                        border-bottom: 3px solid #007bff;
                    }

                    .header h1 {
                        color: #2c3e50;
                        margin: 0 0 10px 0;
                        font-size: 28px;
                    }

                    .header p {
                        color: #7f8c8d;
                        font-size: 14px;
                        margin: 0;
                    }

                    .profile-section {
                        display: flex;
                        align-items: center;
                        margin-bottom: 30px;
                        padding: 20px;
                        background-color: #f8f9fa;
                        border-radius: 8px;
                    }

                    .profile-img {
                        width: 120px;
                        height: 120px;
                        border-radius: 50%;
                        object-fit: cover;
                        border: 3px solid #007bff;
                        margin-right: 25px;
                    }

                    .profile-section h2 {
                        color: #2c3e50;
                        margin: 0 0 8px 0;
                        font-size: 24px;
                    }

                    .profile-section p {
                        margin: 5px 0;
                        color: #34495e;
                    }

                    .section-title {
                        color: #007bff;
                        font-size: 18px;
                        margin: 25px 0 15px 0;
                        padding-bottom: 8px;
                        border-bottom: 2px solid #e0e0e0;
                        text-transform: uppercase;
                    }

                    .info-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                        background-color: white;
                        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                    }

                    .info-table td {
                        padding: 12px 15px;
                        border: 1px solid #e0e0e0;
                    }

                    .info-table tr td:first-child {
                        font-weight: 600;
                        background-color: #f8f9fa;
                        width: 30%;
                    }


                    .patient-info {
                        padding: 20px;
                        background-color: #f8f9fa;
                        border-radius: 8px;
                        border: 1px solid #e0e0e0;
                        white-space: pre-line;
                        line-height: 1.8;
                    }


                    @media (max-width: 768px) {
                        .profile-section {
                            flex-direction: column;
                            text-align: center;
                        }
                        
                        .profile-img {
                            margin-right: 0;
                            margin-bottom: 15px;
                        }
                    }
                </style>
                ';
            $html = $styles . '
                <div class="header">
                    <h1>Fiche Médicale du Patient</h1>
                    <p>Date de génération: '.date('d/m/Y').'</p>
                </div>

                <div class="profile-section">
                    <img src="'.$patient['photo_profil'].'" class="profile-img">
                    <div>
                        <h2>'.$patient['prenom'].' '.$patient['nom'].'</h2>
                        <p>Âge: '.$patient['age'].' ans</p>
                        <p>Genre: '.ucfirst($patient['genre']).'</p>
                    </div>
                </div>

                <h3 class="section-title">Informations de Contact</h3>
                <table class="info-table">
                    <tr>
                        <td>Téléphone</td>
                        <td>'.$patient['telephone'].'</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>'.$patient['email'].'</td>
                    </tr>
                </table>

                <h3 class="section-title">Informations Médicales</h3>
                <table class="info-table">
                    <tr>
                        <td>Taille</td>
                        <td>'.$patient['taille'].' cm</td>
                    </tr>
                    <tr>
                        <td>Poids</td>
                        <td>'.$patient['poids'].' kg</td>
                    </tr>
                    <tr>
                        <td>Groupe Sanguin</td>
                        <td>'.$patient['group_sanguin'].'</td>
                    </tr>
                    <tr>
                        <td>Maladies Chroniques</td>
                        <td>'.$patient['maladies_chroniques'].'</td>
                    </tr>
                </table>

                <h3 class="section-title">Informations Complémentaires</h3>
                <div class="patient-info">
                    '.nl2br($patient['informations']).'
                </div>
            ';

            $mpdf->SetTitle('Fiche Patient - '.$patient['prenom'].' '.$patient['nom']);
            $mpdf->WriteHTML($styles);
            $mpdf->WriteHTML($html);
            $filename = 'pdf/patient_'.$patient['id_patient'].'_'.date('Ymd').'.pdf';
            $mpdf->Output($filename, \Mpdf\Output\Destination::FILE);
            $datee=date('Y-m-d H:i:s');
            $insert_fiche =("
                INSERT INTO fiche_medicale 
                (id_rdv, fichier_pdf, date_creation)
                VALUES ('{$row2['id_rdv']}', '$filename', '$datee')
            ");

            $res=mysqli_query($conn,$insert_fiche);
            if(!$res) {
                echo "Error creating medical record: ";
                exit();
            }
            $insert_historique =("
            INSERT INTO historique 
            (id_patient, id_medecin, id_rdv, date_consultation)
            VALUES ('{$row['id_patient']}', '{$_SESSION['user_id']}','{$row2['id_rdv']}', '$datee')
            ");

            $res=mysqli_query($conn,$insert_historique);
            if(!$res) {
                echo "Error Inserting historique: ";
                exit();
            }
        }
    }
    if(isset($_POST['refuser'])){
        $req="UPDATE rendez_vous set statut='annulé' where id_patient=".$row['id_patient']." and id_medecin=".$_SESSION['user_id'];
        if(mysqli_query($conn,$req)){
            mysqli_free_result($req);
        }
        $req="DELETE from paiement where id_patient =".$row['id_patient']." and id_rdv=".$row2['id_rdv'];
        if(mysqli_query($conn,$req)){
            $nom_doctor=$_SESSION['prenom']." ".$_SESSION['nom'];
            $message = "rendez vous refuser avec docteur " . $nom_doctor;
            $req = "UPDATE notification SET message = '$message' WHERE id_utilisateur = " . $row['id_patient'];
            $res=mysqli_query($conn,$req);
        }
    }
    $sql = "DELETE FROM notification WHERE id_utilisateur = " . $_SESSION['user_id'] . " AND id_patient = " . $row['id_patient'];
    $res=mysqli_query($conn,$sql);
    header("Location: home.php");

}
?>