<?php

    namespace App\Utils\MaterialContainer;
    
    use Illuminate\Http\Request;
    use App\Comment;
    
    trait TaskStoreMaterial {

        public function TaskStore(Request $request){
            $data = $request->all();
            $task['name'] = $data['name'];
            $task['description'] = $data['description'];
            $task['company_id'] = auth('api')->user()->role == 0 ? auth('api')->user()->id : auth('api')->user()->parent_user;
            $task['project_id'] = $data['project'];
            $task['type'] = $data['type'];
            $task['team_id'] = $data['team']['id'];
            $task['created_by'] = auth('api')->user()->id;

            return $task;
        }
        
    }

?>