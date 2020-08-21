<?php

    namespace App\Utils\MaterialContainer;
    use Illuminate\Http\Request;
    use App\User;
    trait UserStoreMaterial {

        public function UserStore(Request $request){
            $user = $request->all();
            if(isset($user['company_type']) && $user['company_type']['id'] != ''){
                $user['company_type'] = $user['company_type']['id'];
            }
            if(isset($user['password']) && $user['password'] != ''){
                $user['password'] = bcrypt($user['password']);
            }
            
            if($user['role'] == 0){
                $user['tenant_id'] = $this->getTenantId();
            } else {
                $user['tenant_id'] = auth('api')->user()->tenant_id;
                $user['parent_user'] = auth('api')->user()->id;
            }
            return $user;
        }

        public function getTenantId(){
            $tenantId = 1;
            $userForTenant = User::where('tenant_id', "!=" , NULL)->orderBy('id','desc')->first();
            if(!empty($userForTenant)){
                $tenantId = $userForTenant->tenant_id + 1;
            }
            return $tenantId;
        }
        
    } 

?>