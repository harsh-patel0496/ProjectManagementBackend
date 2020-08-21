<?php

    namespace App\Utils\MaterialContainer;
    
    use Illuminate\Http\Request;
    use App\User;
    
    trait TeamStoreMaterial {

        public function TeamStore(Request $request){
            $data = $request->all();
            $team['name'] = $data['name'];
            $team['description'] = $data['description'];
            $team['company_id'] = auth('api')->user()->id;
            return $team;
        }
        
    } 

?>