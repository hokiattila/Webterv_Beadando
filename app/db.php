<?php
class DatabaseInteractions {
    public $cars =  array(
        ["Mercedes-Benz", "CLA", "Benzin", "126 HP"],
        ["BMW", "I5", "Elektromos", "80 HP"],
        ["Peugeot", "206", "Benzin", "1233 HP"]
    );

    public function fetchCarData() {
        return $this->cars;
    }

}