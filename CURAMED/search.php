<?php
session_start();
header('Content-Type: text/html; charset=utf-8');


$conn = mysqli_connect('localhost', 'root', '', 'curamed');
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}


$sql = "
    SELECT 
        u.id_utilisateur, u.nom, u.prenom, u.photo_profil,
        m.specialite, m.adresse_cabinet AS adresse, m.experience
    FROM utilisateur u
    JOIN medecin m ON u.id_utilisateur = m.id_medecin
";
$res = mysqli_query($conn, $sql);
if (!$res) {
    die('Query failed: ' . mysqli_error($conn));
}
$doctors = mysqli_fetch_all($res, MYSQLI_ASSOC);
mysqli_free_result($res);


$tempDir = __DIR__ . '/temp';
if (!is_dir($tempDir)) {
    mkdir($tempDir, 0755, true);
}
$sessionId  = session_id();
$inputFile  = "$tempDir/{$sessionId}_input.json";
$outputFile = "$tempDir/{$sessionId}_output.json";


$inputData = [
    'query'     => $_POST['cherche'] ?? '',
    'specialty' => $_POST['specialite'] ?? '',
    'doctors'   => $doctors
];
file_put_contents($inputFile, json_encode($inputData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));


$pythonPath = 'C:\\Program Files\\python.exe';
$scriptPath = __DIR__ . '\\indexation\\found_med.py';

$cmd = '"' . $pythonPath . '" "' . $scriptPath . '" "' . $inputFile . '" "' . $outputFile . '" 2>&1';
exec($cmd, $output, $returnCode);


if ($returnCode !== 0 || !file_exists($outputFile)) {
    echo "<h2>❌ Erreur lors de l'exécution du script Python</h2>";
    echo "<p><strong>Commande:</strong> $cmd</p>";
    echo "<p><strong>Code de retour:</strong> $returnCode</p>";
    echo "<pre><strong>Sortie Python:</strong>\n" . htmlspecialchars(implode("\n", $output)) . "</pre>";
    echo "<p><strong>Contenu de l'input JSON:</strong></p><pre>" . htmlspecialchars(file_get_contents($inputFile)) . "</pre>";
    exit;
}


$results = json_decode(file_get_contents($outputFile), true) ?: [];
unlink($inputFile);
unlink($outputFile);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Résultats de recherche</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <h1 class="mb-4">Résultats pour “<?= htmlspecialchars($_POST['search'] ?? '') ?>”</h1>

    <?php if ($results): ?>
      <div class="row">
        <?php foreach ($results as $doc): ?>
          <div class="col-md-6 mb-4">
            <div class="p-3 bg-white rounded shadow-sm">
              <div class="d-flex align-items-center mb-3">
                <?php if ($doc['photo']): ?>
                  <img src="<?= htmlspecialchars($doc['photo']) ?>" class="rounded-circle me-3" width="64" height="64" alt="photo">
                <?php endif; ?>
                <div>
                  <h5 class="mb-0"><?= htmlspecialchars($doc['name']) ?></h5>
                  <small class="text-muted"><?= htmlspecialchars($doc['specialty']) ?></small>
                </div>
              </div>
              <p><strong>Adresse:</strong> <?= htmlspecialchars($doc['address']) ?></p>
              <p><strong>Expérience:</strong> <?= htmlspecialchars($doc['experience']) ?> ans</p>
              <p><strong>Score de pertinence:</strong> <?= round($doc['score'] * 100) ?>%</p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-warning">Aucun médecin trouvé pour cette recherche.</div>
    <?php endif; ?>
  </div>
</body>
</html>
