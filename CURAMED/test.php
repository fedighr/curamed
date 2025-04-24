<?php

$conn = new mysqli("localhost", "root", "", "curamed");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$diseases = [];
$asked = [];
$results = [];
$showResults = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $diseases = json_decode($_POST['diseases'], true);
    $asked = json_decode($_POST['asked'], true);
    $answer = $_POST['answer'];
    $currentSymptom = $_POST['symptom_id'];

    if ($currentSymptom && $answer) {

        $diseases = array_filter($diseases, function($disease) use ($currentSymptom, $answer) {
            $hasSymptom = in_array($currentSymptom, $disease['symptomes']);
            return ($answer === 'yes') ? $hasSymptom : !$hasSymptom;
        });
        

        $diseases = array_values($diseases);
        if($answer==="yes"){
        $asked[] = $currentSymptom;}


        if (count($diseases) <1 && count($asked)!=0) {
            $results = $diseases;
            $showResults = true;
        }
    }
} else {

    $sql = "SELECT id_maladie, GROUP_CONCAT(id_symptome) AS symptomes FROM maladi_symtome GROUP BY id_maladie;";
    $res = mysqli_query($conn, $sql);
    
    while ($row = mysqli_fetch_assoc($res)) {
        $diseases[] = [
            'id' => $row['id_maladie'],
            'symptomes' => explode(',', $row['symptomes'])
        ];
    }
}

$nextSymptom = null;
$symptomCounts = [];
foreach ($diseases as $disease) {
    foreach ($disease['symptomes'] as $symptom) {
        if (!in_array($symptom, $asked)) {
            $symptomCounts[$symptom] = ($symptomCounts[$symptom] ?? 0) + 1;
        }
    }
}

if (!empty($symptomCounts)) {
    arsort($symptomCounts);
    $nextSymptom = key($symptomCounts);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Symptom Checker</title>
</head>
<body>
    <h1>Symptom Questions</h1>

    <?php if ($showResults || empty($diseases)): ?>
        <?php if (!empty($results)): ?>
            <h3>Possible Diseases:</h3>
            <ul>
                <?php foreach ($results as $result): ?>
                    <li>Disease : <?php $res=mysqli_query($conn,"SELECT nom,specialite from maladies where id_maladie=".$result['id']);
                        $row=mysqli_fetch_assoc($res);
                        echo $result['id'];
                        echo"  ";
                        echo $row['nom'];
                        echo"  ";
                        echo $row['specialite'];?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Vous ette en bon santé rabbi yberek</p>
        <?php endif; ?>
        <a href="test.php">Start Over</a>

    <?php elseif ($nextSymptom): ?>
        <?php
        $sql = "SELECT question FROM questions WHERE id_symptome = " . (int)$nextSymptom;
        $res = mysqli_query($conn, $sql);
        $question = mysqli_fetch_assoc($res)['question'] ?? 'Unknown question';
        ?>
        
        <form method="POST">
            <h3><?= htmlspecialchars($question) ?></h3>
            <input type="hidden" name="symptom_id" value="<?= $nextSymptom ?>">
            <input type="hidden" name="diseases" value="<?= htmlspecialchars(json_encode($diseases)) ?>">
            <input type="hidden" name="asked" value="<?= htmlspecialchars(json_encode($asked)) ?>">
            
            <button type="submit" name="answer" value="yes">Yes</button>
            <button type="submit" name="answer" value="no">No</button>
        </form>
        <p>Remaining possible diseases: <?= count($diseases)?></p>

    <?php else: ?>
        <ul>
            <?php foreach ($diseases as $disease): ?>
                <li>Disease ID: <?php $res=mysqli_query($conn,"SELECT nom,specialite from maladies where id_maladie=".$disease['id']);
                        $row=mysqli_fetch_assoc($res);
                        echo $disease['id'];
                        echo"  ";
                        echo $row['nom'];
                        echo"  ";
                        echo $row['specialite'];?></li>
            <?php endforeach; ?>
        </ul>
        <a href="test.php">Start Over</a>
    <?php endif; ?>

</body>
</html>
<?php $conn->close(); ?>