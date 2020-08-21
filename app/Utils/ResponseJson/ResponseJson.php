<?php

namespace App\Utils\ResponseJson;

class ResponseJson {


    //Prepare preety response

    public function result($status,$message = '',$module = '',$data = []){
        $result = array();
        $result['status'] = $status;
        if(isset($message) && $message != ''){
            $result['message'] = $message;
        }
        if(isset($module) && $module != '' && !empty($data)){
            $result[$module] = $data;
        }

        return $result;
    }

    public function error($errMessage = "Something went wrong!",$status = 500){
        $result = $this->result(false,$errMessage);
        return response()->json($result,$status);
    }

    public function success($successMessage = "",$module = "",$data = []){
        $result = $this->result(true,$successMessage,$module,$data);
        return response()->json($result,200);
    }
}

?>