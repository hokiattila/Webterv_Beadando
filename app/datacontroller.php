<?php
include_once('db.php');


class DataController {
    private DatabaseInteractions $db;
    private int $pageLimit;

    public function __construct() {
        $this->db = new DatabaseInteractions;
        $this->pageLimit = 5;
    }
    public function carData($offset) : array {
        if($offset < 0) $offset = 0;
        return $this->db->fetchCarData($offset, $this->pageLimit);
    }

    public function pageCount(): int {
       return  ceil($this->db->getCarRowCount() / $this->pageLimit);
    }

    public function setLimit($newlimit) : void {
        if($newlimit >= 0) $this->pageLimit = $newlimit;
    }

    public function getLimit() : int {
        return $this->pageLimit;
    }



}


$controller = new DataController;

if(isset($_POST['marka_gomb'])) {

}

if(isset($_POST['uj_gomb'])) {
    $record = array($_POST['marka'],$_POST['modell'],$_POST['meghajtas'],(int) $_POST['loero']);
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