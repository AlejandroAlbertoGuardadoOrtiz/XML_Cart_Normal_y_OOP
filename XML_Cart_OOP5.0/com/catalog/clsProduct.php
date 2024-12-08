<?php
class clsProduct {
    private $id;
    private $name;
    private $price;
    private $quantity;

    public function __construct($id, $name, $price, $quantity) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function getPrice(){
        return $this->price;
    }

    public function getQuantity(){
        return $this->quantity;
    }
}
?>