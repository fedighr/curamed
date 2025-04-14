<?php
session_start();
header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "", "curamed");

if (!$conn) {
    die(json_encode(['error' => 'Database connection failed']));
}

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    
    $query = "SELECT u.*, m.specialite 
              FROM utilisateur u
              LEFT JOIN medecin m ON u.id_utilisateur = m.id_medecin
              WHERE u.id_utilisateur = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        echo json_encode($userData);
    } else {
        echo json_encode(['error' => 'Utilisateur non trouvé']);
    }
} else {
    echo json_encode(['error' => 'ID utilisateur non fourni']);
}

mysqli_close($conn);
?>