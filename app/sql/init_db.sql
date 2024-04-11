CREATE USER IF NOT EXISTS 'car_service_user'@'localhost' IDENTIFIED BY 'carpsw';
CREATE DATABASE IF NOT EXISTS web1_project;
USE web1_project;

GRANT ALL ON web1_project.* TO 'car_service_user'@'localhost';

CREATE TABLE IF NOT EXISTS cars(
	id INT AUTO_INCREMENT PRIMARY KEY,
	brand VARCHAR(50) NOT NULL,
    modell VARCHAR(30) NOT NULL,
	build_year SMALLINT NOT NULL,
    door_count ENUM('5','3','2','0') NOT NULL,
	color VARCHAR(12) NOT NULL,
	weight SMALLINT NULL,
	power SMALLINT NULL,
	con ENUM('Totálkár', 'Újszerű', 'Új', 'Viseltes') NOT NULL,
	fuel_type ENUM('Benzin', 'Diesel', 'Elektromos', 'Gázüzem', 'Hidrogén') NOT NULL,
	price INT NOT NULL, 
	VIN VARCHAR(50),
	UNIQUE(VIN)
);


CREATE TABLE IF NOT EXISTS users(
	username VARCHAR(50) PRIMARY KEY,
	hashed_psw VARCHAR(256) NOT NULL,
	role VARCHAR(12) NOT NULL,
	first_name VARCHAR(50) NOT NULL,
	last_name VARCHAR(50) NOT NULL,
	birth_date DATE NOT NULL,
	gender ENUM('Férfi', 'Nő') NOT NULL,
	join_date DATETIME NOT NULL,
	phone_number VARCHAR(30) NOT NULL,
	email VARCHAR(50) NOT NULL
);	


CREATE TABLE IF NOT EXISTS favorites(
	id INT AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(50) NOT NULL, 
	car_VIN VARCHAR(50) NOT NULL,
	fav_date DATETIME NOT NULL,
	FOREIGN KEY(username) REFERENCES users(username) ON DELETE CASCADE,
	FOREIGN KEY(car_VIN) REFERENCES cars(VIN) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS reservations(
	id INT AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(50) NOT NULL,
	car_VIN VARCHAR(50) NOT NULL,
	start_time DATETIME NOT NULL, 
	end_time DATETIME NOT NULL,
	FOREIGN KEY(username) REFERENCES users(username) ON DELETE CASCADE,
	FOREIGN KEY(car_VIN) REFERENCES cars(VIN) ON DELETE CASCADE
);