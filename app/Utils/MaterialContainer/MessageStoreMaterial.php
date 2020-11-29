<?php

    namespace App\Utils\MaterialContainer;
    use Illuminate\Http\Request;
    use App\Message;

    trait MessageStoreMaterial {

        public function MessageStore(Request $request){
            $data = $request->all();
            
            $message['message'] = $data['message'];
            $message['sender_id'] = auth('api')->user()->id;
            $message['read'] = 0;

            if($data['is_team']){
                $message['team_id'] = $data['team_id'];
            } else {
                $message['receiver_id'] =  $data['receiver_id'];
            }
            $message['created_at'] = date('Y-m-d H:i:s');
            $message['updated_at'] = date('Y-m-d H:i:s');

            return $message;
        }
        
    } 

?>