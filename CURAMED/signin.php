<?php
session_start();
header('Content-Type: application/json');


$max_attempts = 5;
$lockout_time = 10;


if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = [
        'count' => 0,
        'last_attempt' => 0,
        'locked_until' => 0
    ];
}


if (time() < $_SESSION['login_attempts']['locked_until']) {
    echo json_encode([
        'success' => false,
        'message' => 'Trop de tentatives. Veuillez réessayer dans '. 
                     ceil(($_SESSION['login_attempts']['locked_until'] - time())/60).' minutes.'
    ]);
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "curamed");

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["mdp"]);

    $sql = "SELECT id_utilisateur, nom, prenom, email, mot_de_passe, photo_profil, role, type_utilisateur 
            FROM utilisateur 
            WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Erreur système']);
        exit();
    }

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['mot_de_passe'])) {

            $_SESSION['login_attempts'] = [
                'count' => 0,
                'last_attempt' => 0,
                'locked_until' => 0
            ];


            $_SESSION['user_id'] = $user['id_utilisateur'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['photo'] = $user['photo_profil'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['type'] = $user['type_utilisateur'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];

            echo json_encode(['success' => true]);
        } else {

            $_SESSION['login_attempts']['count']++;
            $_SESSION['login_attempts']['last_attempt'] = time();


            if ($_SESSION['login_attempts']['count'] >= $max_attempts) {
                $_SESSION['login_attempts']['locked_until'] = time() + $lockout_time;
                $message = 'Trop de tentatives. Compte bloqué temporairement.';
            } else {
                $remaining_attempts = $max_attempts - $_SESSION['login_attempts']['count'];
                $message = 'Mot de passe incorrect. Tentatives restantes: '.$remaining_attempts;
            }

            echo json_encode([
                'success' => false,
                'message' => $message
            ]);
        }
    } else {

        $_SESSION['login_attempts']['count']++;
        $_SESSION['login_attempts']['last_attempt'] = time();

        if ($_SESSION['login_attempts']['count'] >= $max_attempts) {
            $_SESSION['login_attempts']['locked_until'] = time() + $lockout_time;
            $message = 'Trop de tentatives. Compte bloqué temporairement.';
        } else {
            $remaining_attempts = $max_attempts - $_SESSION['login_attempts']['count'];
            $message = 'Aucun utilisateur trouvé avec cet e-mail. Tentatives restantes: '.$remaining_attempts;
        }

        echo json_encode([
            'success' => false,
            'message' => $message
        ]);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>