<?php

    namespace App\Utils\MaterialContainer;
    
    use Illuminate\Http\Request;
    use App\Comment;
    
    trait CommentStoreMaterial {

        public function CommentStore(Request $request){
            $data = $request->all();
            $comment['comment'] = $data['comment'];
            $comment['company_id'] = auth('api')->user()->role == 0 ? auth('api')->user()->id : auth('api')->user()->parent_user;
            $comment['user_id'] = auth('api')->user()->id;
            return $comment;
        }
        
    }

?>