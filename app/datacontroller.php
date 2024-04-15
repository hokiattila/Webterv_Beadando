<?php
include_once('db.php');
session_start();

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


    public function generateToken() : string {
        try {
            $token = bin2hex(random_bytes(32));
            $_SESSION['token'] = $token;
            return $token;
        } catch (Exception $e) {
            error_log('Hiba a token generálásakor: ' . $e->getMessage());
            return 'Hiba történt a token generálásakor.';
        } catch (Error $e) {
            error_log('Rendszerhiba a token generálásakor: ' . $e->getMessage());
            return 'Rendszerhiba történt a token generálásakor.';
        }
    }

    public function loginController($username_input, $password_input, $token) : void {
        if($token != $_SESSION['token']) header("Location: ../index.php?error=tokenmissmatch");
        else if(empty($username_input)) header("Location: ../index.php?error=empty_username");
        else if(empty($password_input)) header("Location: ../index.php?error=empty_password");
        else {
            $res = $this->db->fetchUserData($username_input, $password_input);
            if($res[0]['username'] == $username_input && password_verify($password_input, $res[0]['hashed_psw'])) {
                $_SESSION['username'] = $res[0]['username'];
                $_SESSION['role'] = $res[0]['role'];
                header("Location: ../index.php");
            } else {
                header("Location: ../index.php?error=invalid_credentials");
            }
        }
    }

}



$controller = new DataController;

if(isset($_POST['login-btn'])) {
    $controller->loginController($_POST['username'], $_POST['password'], $_POST['token']);
}

