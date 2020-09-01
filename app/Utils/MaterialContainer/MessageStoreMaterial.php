<?php

    namespace App\Utils\MaterialContainer;
    use Illuminate\Http\Request;
    use App\Message;

    trait MessageStoreMaterial {

        public function MessageStore(Request $request){
            $data = $request->all();
            
            $message['message'] = $data['message'];
            $message['user_id'] = auth('api')->user()->id;
            $message['read'] = false;
            $message['created_at'] = date('Y-m-d H:i:s');
            $message['updated_at'] = date('Y-m-d H:i:s');

            return $message;
        }
        
    } 

?>