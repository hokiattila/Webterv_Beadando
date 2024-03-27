<?php
class DatabaseInteractions {
    public function fetchCarData() : array {
        $carsArray = [];
        $filePath = __DIR__.'\database.txt';
        if (file_exists($filePath)) {
            $fileHandle = fopen($filePath, "r");
            while (($line = fgets($fileHandle)) !== false) {
                $carDetails = explode(" ", $line, 4);
                if (count($carDetails) == 4) {
                    $carsArray[] = $carDetails;
                }
            }
            fclose($fileHandle);
        } else {
            $carsArray[] = ['Error' => 'File not found.'];
        }
        return $carsArray;
    }


    public function fetchCarsByBrand(string $brand) : array|false {
        $CARS = $this->fetchCarData();
        $foundCars = [];
        foreach ($CARS as $car) {
            if ($car[0] === $brand) {
                $foundCars[] = $car;
            }
        }
        return !empty($foundCars) ? $foundCars : false;
    }

    public function fetchBrandNames() : array|false {
        $brandNames = [];
        $CARS = $this->fetchCarData();
        foreach ($CARS as $car) {
            $brandNames[] = $car[0];
        }
        return !empty($brandNames) ? $brandNames : false;
    }

    public function getDistinctBrands() : array|false {
        $brands = $this->fetchBrandNames();
        if(empty($brands)) return false;
        return array_combine($brands,$brands);
    }


    public function insertCar(array $carDetails) : void {
        $filePath = 'database.txt';
        $carLine = implode(" ", $carDetails) . "\n";
        $fileHandle = fopen($filePath, "a");
        fwrite($fileHandle, $carLine);
        fclose($fileHandle);
    }


}