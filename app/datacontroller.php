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

    public function logoutController($token) : void {
        if($token != $_SESSION['token']) header("Location: ../index.php?error=tokenmissmatch");
        $_SESSION = array();
        session_destroy();
        header("Location: ../index.php");
    }

    public function registerController($token, $username_input, $password_input, $password_conf, $email, $lastname, $firstname, $szuldatum, $nem, $telefonszam) : void {
        if($token != $_SESSION['token']) header("Location: ../index.php?error=tokenmissmatch");
        else if(empty($username_input) || empty($password_input) || empty($password_conf) || empty($email) || empty($lastname) || empty($firstname) || empty($szuldatum) || empty($nem) || empty($telefonszam)) header("Location: ../index.php?error=missing_credentials");
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) header("Location: ../index.php?error=invalid_email");
        else if($password_input != $password_conf) header("Location: ../index.php?error=psw_missmatch");
        else if((!$this->checkString($firstname, "ONLYALPHABET")) || (!$this->checkString($lastname, "ONLYALPHABET")))  header("Location: ../index.php?error=invalid_credentials");
        else if((!$this->checkString($telefonszam, "ONLYNUM")))  header("Location: ../index.php?error=invalid_phone");
        else if(!$this->validateCredentials($username_input)) header("Location: ../index.php?error=forbidden_value");
        else {
            $hashed_psw = password_hash($password_input, PASSWORD_DEFAULT);
            $this->db->insertUserData($username_input, $hashed_psw, $email, $lastname, $firstname, $szuldatum, $nem, $telefonszam );
            $this->loginController($username_input, $password_input, $token);
        }
    }

    private function validateCredentials($input) : bool {
        $dangerousCharacters = ["'", "\"", ";", "--", "#"];
        foreach ($dangerousCharacters as $char) {
            if (str_contains($input, $char)) {
                return false;
            }
        }
        return true;
    }

    private function checkString($str, $mode) : bool {
        return match ($mode) {
            'ONLYALPHABET' => !preg_match('/\d/', $str),
            'ONLYNUM' => !preg_match('/[a-zA-Z]/', $str),
            default => throw new InvalidArgumentException("Invalid mode specified"),
        };
    }

}

$controller = new DataController;

if(isset($_POST['login-btn'])) {
    $controller->loginController($_POST['username'], $_POST['password'], $_POST['token']);
}

if(isset($_POST['logout-btn'])) {
    $controller->logoutController($_POST['token']);
}

if(isset($_POST['register-btn'])) {
    $controller->registerController($_POST['token'], $_POST['username'], $_POST['password'], $_POST['password_conf'], $_POST['email'], $_POST['lastname'], $_POST['firstname'], $_POST['szuldatum'], $_POST['nem'], $_POST['telefonszam']);
}