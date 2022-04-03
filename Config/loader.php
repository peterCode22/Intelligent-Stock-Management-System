<?php

function load_class($className){
    $path = $_SERVER['DOCUMENT_ROOT'] . '/dsp/Domain/' . $className . '.php';

    if(file_exists($path)){
        require_once $path;
    }
}

spl_autoload_register('load_class');

?>