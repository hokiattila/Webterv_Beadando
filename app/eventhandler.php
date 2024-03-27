<?php
require "db.php";
if(isset($_POST['gomb'])) {
    $marka = $_POST['marka'];
    $CarInventory = new DatabaseInteractions();
    $array = $CarInventory->fetchCarData();
    $distinct_car_brands = $CarInventory->getDistinctBrands();
    require "../pub/car.php";
}