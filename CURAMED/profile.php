<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "curamed");

if (!$conn) {
    die("Erreur de connexion à la base de données.");
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    die("Utilisateur non connecté.");
}

$req = "SELECT * FROM utilisateur WHERE id_utilisateur = $user_id";
$res = mysqli_query($conn, $req);

if ($res && mysqli_num_rows($res) > 0) {
    $table = mysqli_fetch_assoc($res);
} else {
    die("Erreur lors de la récupération des données utilisateur.");
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Profil Utilisateur</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2 class="mb-4">Informations du Profil</h2>

    <table class="table table-bordered table-striped bg-white shadow">
      <tbody>
        <tr>
          <th>ID</th>
          <td><?php echo htmlspecialchars($table["id_utilisateur"]); ?></td>
        </tr>
        <tr>
          <th>Prénom</th>
          <td><?php echo htmlspecialchars($table["prenom"]); ?></td>
        </tr>
        <tr>
          <th>Nom</th>
          <td><?php echo htmlspecialchars($table["nom"]); ?></td>
        </tr>
        <tr>
          <th>Âge</th>
          <td><?php echo htmlspecialchars($table["age"]); ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</body>
</html>
