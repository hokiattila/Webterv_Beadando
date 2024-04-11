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

if(isset($_POST['register-btn'])) {
    $password_match = (bool) $_POST['password'] === $_POST['password_conf'];
    $all_filled = !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['password_conf']);
    if(!$password_match) header("Location ../index.php?error=password_miss_match");
    if(!$all_filled) header("Location ../index.php?error=missing_param");
    $uname = $_POST['username'];
    $password = $_POST['password'];
    $password_conf = $_POST['password_conf'];
    echo $uname.' '.$password.' '.$password_conf;
}