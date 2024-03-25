<?php
    require "../app/db.php";
    $array = new DatabaseInteractions();
    $array = $array->fetchCarData();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <title>Autóink</title>
</head>
<body>
    <h1>Jelenlegi eladó autóink:</h1>
    <?php
    foreach ($array as $car) {
        echo "Márka: " . $car[0] . ", Modell: " . $car[1] . ", Üzemanyag: " . $car[2] . ", Lóerő: " . $car[3] . "<br>";
    }
    ?>
</body>
</html>