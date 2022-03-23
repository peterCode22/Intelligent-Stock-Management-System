<?php

function load_class($className){
    $path = 'classes/' . $className . '.php';

    if(file_exists($path)){
        require_once $path;
    }
}

spl_autoload_register('load_class');

?>