<?php
class DatabaseInteractions {
    private string $servername = "roundhouse.proxy.rlwy.net";
    private string $username = "root";
    private string $password = "ilFMYGqpWoJJvyuvmyowDeZJvbaLBRZp";
    private int $port = 45698;
    private string $database = "railway";

    function dbConnection($mode = "DB") : PDO {
        return $mode != "NODB"
            ? new PDO("mysql:host=$this->servername;dbname=$this->database;port=$this->port;charset=utf8", $this->username, $this->password)
            : new PDO("mysql:host=$this->servername;port=$this->port;charset=utf8", $this->username, $this->password);
    }

    function dbInit() : void{
        try {
            $conn = $this->dbConnection("NODB");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $conn->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
            $stmt->execute([$this->database]);
            if ($stmt->rowCount() == 0) {
                if($this->validateDatabaseName($this->database))
                $conn->exec("CREATE DATABASE `$this->database`");
                $conn = $this->dbConnection();
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $tables = [
                    "CREATE TABLE IF NOT EXISTS users(
	                    username VARCHAR(50) PRIMARY KEY,
	                    hashed_psw VARCHAR(256) NOT NULL,
	                    role ENUM('admin','user') NOT NULL,
	                    first_name VARCHAR(50) NOT NULL,
	                    last_name VARCHAR(50) NOT NULL,
	                    birth_date DATE NOT NULL,
	                    gender ENUM('Férfi', 'Nő') NOT NULL,
	                    join_date DATE NOT NULL,
	                    phone_number VARCHAR(30) NOT NULL DEFAULT 'unknown',
	                    email VARCHAR(50) NOT NULL
                    )",
                    "CREATE TABLE IF NOT EXISTS cars(
	                    id INT AUTO_INCREMENT PRIMARY KEY,
	                    brand VARCHAR(50) NOT NULL,
                        modell VARCHAR(30) NOT NULL,
	                    build_year SMALLINT NOT NULL,
                        door_count SMALLINT NOT NULL,
	                    color VARCHAR(12) NOT NULL,
	                    weight SMALLINT NULL,
	                    power SMALLINT NULL,
	                    con ENUM('Totálkár', 'Újszerű', 'Új', 'Viseltes') NOT NULL,
	                    fuel_type ENUM('Benzin', 'Diesel', 'Elektromos', 'Gázüzem', 'Hidrogén') NOT NULL,
	                    price INT NOT NULL, 
	                    VIN VARCHAR(50),
	                    UNIQUE(VIN)
                    )",
                    "CREATE TABLE IF NOT EXISTS favorites(
	                    id INT AUTO_INCREMENT PRIMARY KEY,
	                    username VARCHAR(50) NOT NULL, 
	                    car_VIN VARCHAR(50) NOT NULL,
	                    fav_date DATETIME NOT NULL,
	                    FOREIGN KEY(username) REFERENCES users(username) ON DELETE CASCADE,
	                    FOREIGN KEY(car_VIN) REFERENCES cars(VIN) ON DELETE CASCADE
                    )",
                    "CREATE TABLE IF NOT EXISTS reservations(
	                    id INT AUTO_INCREMENT PRIMARY KEY,
	                    username VARCHAR(50) NOT NULL,
	                    car_VIN VARCHAR(50) NOT NULL,
	                    start_time DATETIME NOT NULL, 
	                    end_time DATETIME NOT NULL,
                        approved ENUM('igen', 'nem'),
	                    FOREIGN KEY(username) REFERENCES users(username) ON DELETE CASCADE,
	                    FOREIGN KEY(car_VIN) REFERENCES cars(VIN) ON DELETE CASCADE
                )"
                ];
                foreach ($tables as $sql) {
                    $conn->exec($sql);
                }
                $stmt  = $conn->prepare("INSERT INTO users VALUES (?,?,?,?,?,?,?,?,?,?)");
                $stmt->execute(["admin",password_hash("admin", PASSWORD_DEFAULT), "admin", "Admin", "Admin", date('Y-m-d'), "Férfi", date('Y-m-d'), "1","admin@carsales.com"]);

                $stmt = $conn->prepare("INSERT INTO cars VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt->execute(["NULL", "Peugeot", "206", date('Y-m-d', strtotime('2004-05-22')), "5","fehér", "1","110","Újszerű","Benzin", "400000", "SADJN3331JNCDS"]);
                $stmt->execute(["NULL", "BMW", "M3", date('Y-m-d', strtotime('2011-05-22')), "5","fekete", "2","220","Viseltes","Diesel", "1200000", "ASDFASD23232323"]);
                $stmt->execute(["NULL", "Mercedes-Benz", "CLA", date('Y-m-d', strtotime('2017-02-12')), "5","fekete", "3","180","Új","Benzin", "13000000", "FKNGMDFJKGNDJF232"]);
                $stmt->execute(["NULL", "Audi", "R8", date('Y-m-d', strtotime('2002-05-22')), "5","szürke", "1","110","Totálkár","Benzin", "120000", "SDGFDFSGSFDG33"]);
                $stmt->execute(["NULL", "Mazda", "RX7", date('Y-m-d', strtotime('1992-05-22')), "5","fehér", "1","110","Újszerű","Benzin", "500000", "SGFSDGDFSG"]);
            }
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
    public function fetchCarData($offset, $limit) : array {
        $pdo = $this->dbConnection();
        $sql = "SELECT * FROM cars LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function validateDatabaseName($databaseName): bool {
        return preg_match('/^[a-zA-Z0-9_]+$/', $databaseName);
    }

    function getCarRowCount() : int {
        $pdo = $this->dbConnection();
        $stmt = $pdo->query("SELECT COUNT(*) FROM cars");
        $rowCount = $stmt->fetchColumn();
        return (int) $rowCount;
    }


    public function fetchCarsByBrand(string $brand) : array|bool {
            return false;
    }

    public function fetchBrandNames() : array|bool {
        return  false;
    }

    public function getDistinctBrands() : array|bool {
        return false;
    }


    public function insertCar(array $carDetails) : void {

    }


}