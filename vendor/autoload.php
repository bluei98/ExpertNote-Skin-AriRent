<?php
spl_autoload_register(function ($class_name){
    $class_name = __DIR__."/".str_replace('\\', '/', $class_name);
    if(file_exists($class_name . '.php')) {
        include $class_name . '.php';
    }
    else {
        // echo 'Class not found';
    }
});