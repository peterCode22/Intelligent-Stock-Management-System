<?php
class Item{
    //Properties
    private $id;
    private $name;
    private $price;
    private $quantity;
    private $itemValue;

    //Class constructor
    function __construct($id, $name, $price, $quantity){
        $this->setID($id);
        $this->setName($name);
        $this->setPrice($price);
        $this->setQuantity($quantity);
        $this->updateValue();
    }

    //Methods
    public function getID(){
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

    public function getValue(){
        return $this->itemValue;
    }

    private function setID($id){
        $this->id = $id;
    }

    private function setName($name){
        $this->name = $name;
    }

    private function setPrice($price){
        $this->price = $price;
    }

    private function setValue($val){
        $this->itemValue = $val;
    }

    public function setQuantity($quantity){
        $this->quantity = $quantity;
        $this->updateValue();
    }

    private function updateValue(){
        $this->setValue($this->getPrice() * $this->getQuantity());
    }
}

?>