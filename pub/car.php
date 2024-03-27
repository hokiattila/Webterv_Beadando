<?php
    require "../app/eventhandler.php";
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
    <br>
    <form method="POST" action="../app/eventhandler.php">
        <h2>Keresés</h2>
        <p>Márka:</p>
    <select name="marka">
        <?php
            foreach($distinct_car_brands as $brands) {
                echo "<option>$brands</option>";
            }
        ?>
    </select>
        <input type="submit" name="gomb" value="Keresés">
    </form>
</body>
</html>