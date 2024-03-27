<?php
    session_start();
    include_once("app/db.php");
    $db = new DatabaseInteractions;
    $all = $db->fetchCarData();
    $all_brands = $db->getDistinctBrands();
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
    foreach ($all as $car) {
        echo "Márka: " . $car[0] . ", Modell: " . $car[1] . ", Üzemanyag: " . $car[2] . ", Lóerő: " . $car[3] . "HP<br>";
    }
    ?>
    <br>
    <form method="POST" action="app/eventhandler.php">
        <h2>Keresés</h2>
        <p>Márka:</p>
    <select name="marka">
        <?php
            foreach($all_brands as $brands) {
                echo "<option>$brands</option>";
            }
        ?>
    </select>
        <input type="submit" name="marka_gomb" value="Keresés">
    </form>
    <br>
    <?php
        if(isset($_SESSION['marka_kereses']) && $_SESSION['marka_kereses'] == 0) echo "Nincs találat!";
        if(isset($_SESSION['marka_kereses']) && $_SESSION['marka_kereses'] != 0) {
            foreach ($_SESSION['marka_kereses'] as $record) {
                echo "Márka: " . $record[0] . ", Modell: " . $record[1] . ", Üzemanyag: " . $record[2] . ", Lóerő: " . $record[3] . "HP <br>";
            }
        }
    ?>
    <br>
    <h2>Használt autó regisztrálása:</h2>
    <form method="POST" action="app/eventhandler.php">
        <input type="text" name="marka" placeholder="Autó márka" minlength="3" max="12" required/>
        <input type="text" name="modell" placeholder="Modell" min="2" maxlength="20" required/>
        <select name="meghajtas">
            <option value="Benzin">Benzin</option>
            <option value="Diesel">Diesel</option>
            <option value="Electric">Electric</option>
        </select>
        <input type="number" name="loero" placeholder="Lóerő" min="10" max="2000" required>
        <input type="submit" value="Autó regisztrálása" name="uj_gomb">
    </form>
</body>
</html>