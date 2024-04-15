<?php
    include_once("app/datacontroller.php");
    $controller = new DataController;
    $token = $controller->generateToken();
    $pages = $controller->pageCount();
    if(isset($_GET['page-nr']) && $_GET['page-nr'] > 0 && $_GET['page-nr'] <= $pages){
        $current = (int) $_GET['page-nr'];
    $page = $current - 1;
    $start = $page * $controller->getLimit();
    } else {
        $current = 1;
        $start = 0;
        $page = 0;
    }
    $all = $controller->carData($start);
    ?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="css/car.css">
    <title>Autóink</title>
</head>
<body>
    <h1>Jelenlegi eladó autóink:</h1>
    <?php
    foreach ($all as $car) {
        echo "Márka: " . $car["brand"] . ", Modell: " . $car["modell"] . ", Üzemanyag: " . $car["fuel_type"] . ", Lóerő: " . $car["power"] . "HP <br>";
    }
    ?>
    <div class="page-info">
        Showing <?php echo $current; ?> of <?php echo $pages ?>
    </div>
    <div class="pagination">
        <a href="?page-nr=1">First</a>
        <a href="?page-nr=<?php echo ($current - 1 > 0) ? $current - 1 : 1; ?>">Previous</a>
        <div class="page-numbers">
            <?php for($i = 1; $i<=$pages; $i++): ?>
                <a href="?page-nr=<?php echo $i;?>"><?php echo $i; ?></a>
            <?php endfor;?>
        </div>
        <a href="?page-nr=<?php echo ($current + 1) <= $pages ? $current + 1 : $pages;?>">Next</a>
        <a href="?page-nr=<?php echo $pages ?>">Last</a>
    </div>
    <br>
    <form method="POST" action="app/datacontroller.php">
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
                echo "Márka: " . $record["brand"] . ", Modell: " . $record["modell"] . ", Üzemanyag: " . $record["fuel_type"] . ", Lóerő: " . $record["power"] . "HP <br>";
            }
        }
    ?>
    <br>
    <h2>Használt autó regisztrálása:</h2>
    <form method="POST" action="app/datacontroller.php">
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