<?php
include_once('db.php');
session_start();

class DataController {
    private DatabaseInteractions $db;
    private int $pageLimit;

    private array $error = array(
        "tokenmissmatch" => "Sikertelen token validálás",
        "empty_username" => "Hiányzó felhasználónév vagy jelszó",
        "empty_password" => "Hiányzó felhasználónév vagy jelszó",
        "invalid_credentials" => "Hibás felhasználónév vagy jelszó",
        "invalid_email" => "Nem megfelelő email formátum",
        "psw_missmatch" => "A jelszavak nem egyeznek",
        "forbidden_credentials" => "Nem engedélyezett karakterek",
        "invalid_phone" => "Nem megfelelő telefonszám formátum",
        "nouserlogin" => "A folytatáshoz jelenkezzen be"
    );

    public function __construct() {
        $this->db = new DatabaseInteractions;
        $this->pageLimit = 3;
    }
    public function carData($offset) : array {
        if($offset < 0) $offset = 0;
        return $this->db->fetchCarData($offset, $this->pageLimit);
    }

    public function indCarData($vin) : array|bool
    {
        return $this->db->fetchIndividualCar($vin);
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
        if($token != $_SESSION['token']) header("Location: ../login.php?error=tokenmissmatch");
        else if(empty($username_input)) header("Location: ../login.php?error=empty_username");
        else if(empty($password_input)) header("Location: ../login.php?error=empty_password");
        else {
            $res = $this->db->fetchUserData($username_input, $password_input);
            if($res[0]['username'] == $username_input && password_verify($password_input, $res[0]['hashed_psw'])) {
                $_SESSION['username'] = $res[0]['username'];
                $_SESSION['role'] = $res[0]['role'];
                $_SESSION['login-try'] = "success";
                header("Location: ../index.php");
            } else {
                header("Location: ../login.php?error=invalid_credentials");
            }
        }
    }

    public function logoutController() : void {
        $_SESSION = array();
        session_destroy();
        header("Location: ../index.php");
    }

    public function registerController($token, $username_input, $password_input, $password_conf, $email, $lastname, $firstname, $szuldatum, $nem, $telefonszam) : void {
        if($token != $_SESSION['token']) header("Location: ../register.php?error=tokenmissmatch");
        else if(empty($username_input) || empty($password_input) || empty($password_conf) || empty($email) || empty($lastname) || empty($firstname) || empty($szuldatum) || empty($nem) || empty($telefonszam)) header("Location: ../index.php?error=missing_credentials");
        else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) header("Location: ../register.php?error=invalid_email");
        else if($password_input != $password_conf) header("Location: ../register.php?error=psw_missmatch");
        else if((!$this->checkString($firstname, "ONLYALPHABET")) || (!$this->checkString($lastname, "ONLYALPHABET")))  header("Location: ../register.php?error=forbidden_credentials");
        else if((!$this->checkString($telefonszam, "ONLYNUM")))  header("Location: ../register.php?error=invalid_phone");
        else if(!$this->validateCredentials($username_input)) header("Location: ../register.php?error=forbidden_value");
        else {
            $hashed_psw = password_hash($password_input, PASSWORD_DEFAULT);
            $this->db->insertUserData($username_input, $hashed_psw, $email, $lastname, $firstname, $szuldatum, $nem, $telefonszam );
            $_SESSION["register-success"] = true;
            header("Location: ../login.php");
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

    public function fetchImages($VIN)  {
        $directory = $_SERVER["DOCUMENT_ROOT"]."/img/cars/".$VIN;
        $allowed_types = array('jpg','jpeg','png','gif', 'webp');
        $files = array();
        $dir_handle = @opendir($directory) or die("Hiba történt a könyvtár megnyitásakor!");
        while ($file = readdir($dir_handle)) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $allowed_types)) {
                $files[] = $file;
            }
        }
        closedir($dir_handle);
        return $files;
    }

    public function listBrands() : array|bool {
        return $this->db->fetchBrandNames();
    }

    public function validVIN(string $vin): bool {
        return  $this->db->checkVIN($vin);
    }

    public function favoriteInsertController($vin, $target="car"): void {
        if(empty($_SESSION['username'])) header("Location: login.php?error=nouserlogon");
        $this->db->insertFavorite($_SESSION['username'], $vin);
        $location = match ($target) {
            "index" => "../index.php",
            "user" => "../user.php",
            default => "../car.php?VIN=".$vin,
        };
        header("Location: $location");
    }

    public function favoriteFetchController() : array|false {
        if(empty($_SESSION['username'])) header("Location: login.php?error=nouserlogon");
        return  $this->db->fetchFavoritesByUser($_SESSION['username']);
    }

    public function favoriteRemoveController($vin,  $target="car"): void {
        if(empty($_SESSION['username'])) header("Location: login.php");
        $this->db->removeFavoriteRecord($_SESSION['username'], $vin);
        $location = match ($target) {
            "index" => "../index.php",
            "user" => "../user.php",
            default => "../car.php?VIN=".$vin,
        };
        header("Location: $location");
    }

    public function fetchFavoriteCarController(): array|bool {
        if(empty($_SESSION['username']) || empty($_SESSION['role'])) return false;
        if($_SESSION['role'] == "admin") return false;
        return $this->db->fetchFavoriteCarData($_SESSION['username']);
    }

    public function carController($vin, $brand, $modell, $build_year, $door_count, $color, $weight, $power, $con, $fuel_type, $price) : void {
        $target_dir = $_SERVER['DOCUMENT_ROOT']."/img/cars/" . $vin;
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                die("Hiba a mappa létrehozásakor: $target_dir");
            }
        }
        $file_count = 1;
        if (is_array($_FILES['image']['name'])) {
            foreach ($_FILES['image']['name'] as $key => $name) {
                if ($_FILES['image']['error'][$key] == 0) {
                    $tmp_name = $_FILES['image']['tmp_name'][$key];
                    $file_extension = pathinfo($name, PATHINFO_EXTENSION);
                    $new_filename = sprintf("%02d.%s", $file_count++, $file_extension);
                    $target_file = $target_dir . '/' . $new_filename;

                    if (!move_uploaded_file($tmp_name, $target_file)) {
                        echo "Hiba történt a fájl áthelyezésekor: $new_filename";
                    } else {
                        echo "A fájl sikeresen át lett helyezve: $new_filename<br>";
                    }
                }
            }
        } else {
            echo "Nincs fájl feltöltve, vagy a feltöltött fájl nem megfelelően van kezelve.";
        }
        $this->db->insertCar($vin, $brand, $modell, $build_year, $door_count, $color, $weight, $power, $con, $fuel_type, $price);
        $_SESSION['car-insert'] = "successful";
        header("Location: ../index.php");
    }

    public function deleteController($vin) : void  {
        $directory = $_SERVER['DOCUMENT_ROOT'] . '/img/cars/' . $vin;
        if (is_dir($directory)) {
            $files = glob($directory . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            rmdir($directory);
        } else {
            echo "A könyvtár nem létezik: " . $directory;
        }
        $this->db->deleteCar($vin);
        $_SESSION["delete-car"] = "successful";
        header("Location: ../user.php");
    }

    public function getErrorMessage($code) : string|bool {
        if(array_key_exists($code, $this->error)) return $this->error[$code];
        else return false;
    }
}

$controller = new DataController;

if(isset($_POST['login-btn'])) {
    $controller->loginController($_POST['username'], $_POST['password'], $_POST['token']);
}

if(isset($_GET['logout'])) {
    $controller->logoutController();
}

if(isset($_POST['register-btn'])) {
    $controller->registerController($_POST['token'], $_POST['username'], $_POST['password'], $_POST['password_conf'], $_POST['email'], $_POST['lastname'], $_POST['firstname'], $_POST['szuldatum'], $_POST['nem'], $_POST['telefonszam']);
}

if(isset($_POST['favorite-btn'])) {
    $controller->favoriteInsertController($_POST['vin']);
}

if(isset($_POST['delete-favorite-btn'])) {
    $controller->favoriteRemoveController($_POST['vin']);
}

if(isset($_GET["VIN"]) && isset($_GET["favorite"]) && isset($_GET['target'])) {
    if($_GET["favorite"] == "add") $controller->favoriteInsertController($_GET["VIN"], $_GET["target"]);
    if($_GET["favorite"] == "remove") $controller->favoriteRemoveController($_GET["VIN"], $_GET["target"]);
}

if(isset($_POST['add-btn'])) {
    $controller->carController($_POST['VIN'], $_POST['brand'], $_POST['modell'],$_POST['build_year'],$_POST['door_count'],$_POST['color'],$_POST['weight'],$_POST['power'], $_POST['con'], $_POST['fuel_type'], $_POST['price']);
}

if(isset($_GET['VIN']) && isset($_GET['delete'])) {
    if($_GET['delete'] == "true") {
            $controller->deleteController($_GET['VIN']);
    }
}