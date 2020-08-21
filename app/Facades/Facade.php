<?php

namespace App\Facades;

class Facade {

    //Call the normal method of the class associated with container using __callStatic.
    public static function __callStatic($name,$args){
        return app()->make(static::getFacadeAccessor())->$name(...$args);
    }
}

?>