<?php

namespace App\Facades;

class ResponseJson extends Facade {

    //Return the name of container using which we have bind the ResponseJson class
    protected static function getFacadeAccessor(){
        return 'responseJson';
    }
    
}

?>