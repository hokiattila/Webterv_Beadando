<?php
include_once('db.php');
session_start();

$db = new DatabaseInteractions;

if(isset($_POST['marka_gomb'])) {
    $marka = $_POST['marka'];
    $_SESSION['marka_kereses'] = $db->fetchCarsByBrand($marka);
    if(empty($_SESSION['marka_kereses'])) $_SESSION['marka_kereses'] = 0;
    header("Location: ../car.php");
}

if(isset($_POST['uj_gomb'])) {
    $record = array($_POST['marka'],$_POST['modell'],$_POST['meghajtas'],(int) $_POST['loero']);
    $db->insertCar($record);
    global $CARS;
    print_r($CARS);
    header("Location: ../car.php");
}