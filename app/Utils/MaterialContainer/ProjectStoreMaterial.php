<?php

    namespace App\Utils\MaterialContainer;
    
    use Illuminate\Http\Request;
    use App\Project;
    
    trait ProjectStoreMaterial {

        public function ProjectStore(Request $request){
            $data = $request->all();
            $project['title'] = $data['title'];
            $project['description'] = $data['description'];
            $project['company_id'] = auth('api')->user()->role == 0 ? auth('api')->user()->id : NULL;
            $project['start_date'] = date('Y-m-d H:i:s',strtotime($data['start_date']));
            
            if(isset($data['client'])){
                $project['client_id'] = $data['client']['id']; 
            }

            return $project;
        }
        
    } 

?>