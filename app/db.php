<?php
class DatabaseInteractions {
    public $CARS =  array(
        ["Mercedes-Benz", "CLA", "Benzin", "126 HP"],
        ["Mercedes-Benz", "EQB", "Elektromos", "111 HP"],
        ["BMW", "I5", "Elektromos", "80 HP"],
        ["Peugeot", "206", "Benzin", "1233 HP"]
    );

    public $USERS = array(
        ["1","hokia", 'alma'],
        ["2","koltaia", "körte"],
        ["3", "kovacsm", "szilva"]
    );


    public function fetchCarData() : array {
        return $this->CARS;
    }



    public function fetchCarsByBrand(string $brand) : array|false {
        $foundCars = [];
        foreach ($this->CARS as $car) {
            if ($car[0] === $brand) {
                $foundCars[] = $car;
            }
        }
        return !empty($foundCars) ? $foundCars : false;
    }

    public function fetchBrandNames() : array|false {
        $brandNames = [];
        foreach ($this->CARS as $car) {
            $brandNames[] = $car[0]; // Adding the brand name to the array
        }
        return !empty($brandNames) ? $brandNames : false;
    }

    public function getDistinctBrands() : array|false {
        $brands = $this->fetchBrandNames();
        if(empty($brands)) return false;
        return array_combine($brands,$brands);
    }

}