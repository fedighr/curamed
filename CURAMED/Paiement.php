<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conn = mysqli_connect("localhost", "root", "", "curamed");
if (!$conn) {
    die("Échec de la connexion : " . mysqli_connect_error());
}

$error = '';
$success = '';
$step = 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['verification_code'])) {
        $entered_code = $_POST['verification_code'];
        if ($entered_code == $_SESSION['verification_code']) {
            $step = 2;
        } else {
            $error = "Code de vérification invalide";
            $step = 1;
        }
    } elseif (isset($_POST['payment_method'])) {
        $payment_method = $_POST['payment_method'];
        
        mysqli_begin_transaction($conn);
        
        try {
            $query = "INSERT INTO rendez_vous (id_patient, id_medecin, date_heure, statut)
                      VALUES ('{$_SESSION['user_id']}', '{$_SESSION['id_medecin']}', '{$_SESSION['date']}', 'en attente')";
            mysqli_query($conn, $query);
            $rdv_id = mysqli_insert_id($conn);
            
            $montant = 60;
            $paiement = "INSERT INTO paiement (id_rdv, id_patient, montant, mode_paiement, date_paiement, statut)
                               VALUES ($rdv_id, {$_SESSION['user_id']}, $montant, '$payment_method', NOW(), 'en attente')";
            mysqli_query($conn, $paiement);
            
            $message = "Rendez-vous en attente pour le " . date('d/m/Y H:i', strtotime($_SESSION['date']));
            $notification_p = "INSERT INTO notification (id_utilisateur, message, date_envoi, type, statut)
                                   VALUES ({$_SESSION['user_id']}, '$message', NOW(), 'email', 'envoyé')";
            mysqli_query($conn, $notification_p);

            $message = "Rendez-vous à " . date('d/m/Y H:i', strtotime($_SESSION['date']));
            $notification_m = "INSERT INTO notification (id_utilisateur, message, date_envoi, type, statut,id_patient,id_rdv)
                                   VALUES ({$_SESSION['id_medecin']}, '$message', NOW(), 'email', 'envoyé',{$_SESSION['user_id']},'$rdv_id')";
            mysqli_query($conn, $notification_m);
            
            mysqli_commit($conn);
            
            if ($payment_method == 'cash') {
                $_SESSION['messagee'] = "Rendez-vous confirmé ! Paiement sur place.";
                header("Location: profile.php?id=" . $_SESSION['id_medecin']);
                exit;
            } else {
                $step = 3;
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Erreur lors de la confirmation : " . mysqli_error($conn);
        }
    } elseif (isset($_POST['card_submit'])) {
        $_SESSION['messagee'] = "Paiement par carte effectué avec succès ! Rendez-vous confirmé.";
        header("Location: profile.php?id=" . $_SESSION['id_medecin']);
        exit;
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation de Rendez-vous</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f7fafc, #e6fffa);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .booking-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,198,180,0.15);
            padding: 3rem;
            max-width: 800px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .step-indicator {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
            position: relative;
        }

        .step {
            flex: 1;
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 12px;
            position: relative;
            transition: all 0.3s ease;
        }

        .step.active {
            background: linear-gradient(135deg, #00C6B4, #00A18E);
            color: white;
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,198,180,0.3);
        }

        .payment-option {
            display: flex;
            align-items: center;
            padding: 2rem;
            margin: 1.5rem 0;
            border: 2px solid #e9ecef;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            background: white;
        }

        .payment-option:hover {
            transform: translateY(-3px);
        }

        .payment-option.selected {
            border-color: #00C6B4;
            background: rgba(0,198,180,0.05);
            box-shadow: 0 5px 20px rgba(0,198,180,0.1);
        }

        .payment-option i {
            font-size: 2rem;
            margin-right: 1.5rem;
            color: #00C6B4;
            width: 60px;
            text-align: center;
        }

        .card-form {
            padding: 2rem;
            background: #f8f9fa;
            border-radius: 15px;
            margin-top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.8rem;
            color: #2D2D2D;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #00C6B4;
            box-shadow: 0 0 0 3px rgba(0,198,180,0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #00C6B4, #00A18E);
            color: white;
            border: none;
            padding: 1.2rem 2.5rem;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,198,180,0.3);
        }

        .alert {
            padding: 1.2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .alert-danger {
            background: #fff5f5;
            color: #dc3545;
            border: 2px solid #ffd6d6;
        }

        .alert-success {
            background: #f0fff4;
            color: #2f855a;
            border: 2px solid #c6f6d5;
        }
    </style>
</head>
<body>
    <div class="booking-container">
        <div class="step-indicator">
            <div class="step <?= $step == 1 ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i><br>
                Vérification
            </div>
            <div class="step <?= $step >= 2 ? 'active' : '' ?>">
                <i class="fas fa-credit-card"></i><br>
                Paiement
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <form method="post">
                <h2 class="text-center mb-4">Vérification de l'e-mail</h2>
                <div class="form-group">
                    <label>Code de Confirmation</label>
                    <div class="input-icon">
                        <input type="text" name="verification_code" class="form-control" placeholder="XXXX" required>
                    </div>
                    <small class="text-muted">Vérifiez votre boîte mail pour le code de confirmation</small>
                </div>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-check-circle mr-2"></i>Vérifier
                </button>
            </form>

        <?php elseif ($step == 2): ?>
            <form method="post">
                <h2 class="text-center mb-4">Choix du Mode de Paiement</h2>
                
                <div class="payment-option" onclick="selectPayment(this, 'cash')">
                    <i class="fas fa-money-bill-wave"></i>
                    <div>
                        <h3>Paiement en Espèces</h3>
                        <p class="text-muted">Payez directement à la clinique</p>
                    </div>
                    <input type="radio" name="payment_method" value="cash" hidden>
                </div>

                <div class="payment-option" onclick="selectPayment(this, 'card')">
                    <i class="fas fa-credit-card"></i>
                    <div>
                        <h3>Carte Bancaire</h3>
                        <p class="text-muted">Paiement sécurisé en ligne</p>
                    </div>
                    <input type="radio" name="payment_method" value="visa" hidden>
                </div>

                <button type="submit" class="btn-primary mt-4">
                    <i class="fas fa-arrow-circle-right mr-2"></i>Continuer
                </button>
            </form>

        <?php elseif ($step == 3): ?>
            <form method="post">
                <h2 class="text-center mb-4">Paiement par Carte</h2>
                <div class="card-form">
                    <div class="form-group">
                        <label>Numéro de carte</label>
                        <div class="input-icon">
                            <i class="fas fa-credit-card"></i>
                            <input type="text" class="form-control" placeholder="1234 5678 9012 3456" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date d'expiration</label>
                                <input type="text" class="form-control" placeholder="MM/AA" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cryptogramme</label>
                                <input type="text" class="form-control" placeholder="CVC" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Titulaire de la carte</label>
                        <input type="text" class="form-control" placeholder="Jean Dupont" required>
                    </div>
                </div>

                <button type="submit" name="card_submit" class="btn-primary">
                    <i class="fas fa-lock mr-2"></i>Confirmer le Paiement
                </button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function selectPayment(element, type) {
            // Remove all selections
            document.querySelectorAll('.payment-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selection to clicked element
            element.classList.add('selected');
            
            // Check the corresponding radio button
            const radio = element.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Animate selection
            element.style.transform = 'scale(1.02)';
            setTimeout(() => {
                element.style.transform = 'scale(1)';
            }, 200);
        }
    </script>
</body>
</html>