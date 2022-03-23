<?php
require_once './loader.php';

class Basket{
    //Properties
    private $basketValue;
    private $content;

    //Class constructor
    function __construct(){
        $this->basketValue = 0;
        $this->content = array();
    }

    //Methods
    public function addItem($id, $name, $price, $quantity){
        $item = new Item($id, $name, $price, $quantity);
        $this->content[$item->getID()] = $item;
        $this->updateValue();
    }

    public function removeItem($id){
        if (isset($this->content[$id])){
            unset($this->content[$id]);
            $this->updateValue();
        } else{
            //Invalid ID
        }
    }

    private function updateValue(){
        $totalVal = 0;
        foreach ($this->content as $item){
            $currVal = $item->getValue();
            $totalVal += $currVal;
        }
        $this->setValue($totalVal);
    }

    private function setValue($val){
        $this->basketValue = $val;
    }

    public function getContent(){
        return $this->content;
    }

    public function getValue(){
        return $this->basketValue;
    }

    public function changeQuantity($id, $quantity){
        if ($this->itemExists($id)){
            $this->content[$id]->setQuantity($quantity);
            $this->updateValue();
        } else{
            //Invalid ID
        }
    }

    public function itemExists($id){
        if (isset($this->content[$id])){
            return true;
        }
        else{
            return false;
        }
    }

    public function addQuantity($id, $quantity){
        if (isset($this->content[$id])){
            $currQuantity = $this->content[$id]->getQuantity();
            $this->content[$id]->setQuantity($quantity + $currQuantity);
            $this->updateValue();
        } else{
            //Invalid ID
        }
    }
    
}

?>