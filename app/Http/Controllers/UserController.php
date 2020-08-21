<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use App\User;

use App\Facades\Material;
use App\Facades\ResponseJson;

use App\Mail\UserRegistreted;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(User $user){
        $this->user = $user;
        $this->middleware("auth:api",['except' => ['login','signup','resetPassword','getEmailAddress','changePassword']]);
    }

    public function index()
    {
        //
    }

    public function login(Request $request){
        try{
            $credentials = request(["email","password"]);
            return $this->authenticate($credentials);
        } catch(Excention $e){
            $status = 500;
            $result = ResponseJson::result(false,'Something went wrong!');
            return response()->json($result,$status);
        }
    }

    public function signup(Request $request){

        try{
            $dataToSave = Material::UserStore($request);
            $newUser = $this->user->create($dataToSave);
            $credentials = request(["email","password"]);
            return $this->authenticate($credentials);
        } catch(Excention $e){
            $status = 500;
            $result = ResponseJson::result(false,'Something went wrong!');
            return response()->json($result,$status);
        }
    }

    public function resetPassword(Request $request){
        $username = $request->input('email');
        $verifyEmail = $this->verifyEmail($username);
        if($verifyEmail){
            $this->sendResetPasswordMail($verifyEmail);
        } else {
            $status = 500;
            $result = ResponseJson::result(false,'You will get reset password link if you are registreted with us!');
        }
    }

    public function changePassword(Request $request){
        $password = bcrypt($request->input('password'));
        $updated = $this->user->where('email',request('email'))->update(['password' => $password]);
        if($updated == 1){
            $status = 200;
            $result = ResponseJson::result(true,'Success!');
            return response()->json($result,$status);
        } else {
            $status = 500;
            $result = ResponseJson::result(false,'Something went wrong!');
            return response()->json($result,$status);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //dd($request->all());
        try{
            $dataToUpdate = Material::UserStore($request);
            $updatedUser = $this->user->where('id',$id)->update($dataToUpdate);
            if($updatedUser == 1){
                $updatedUser = $this->userWithCompanyType($id);
            } else {
                $updatedUser = $request->all();
            }
            $result = ResponseJson::result(true,'User Updated Successfully!','user',$updatedUser);
            $status = 200;
            return response()->json($result,$status);
        } catch(Exception $e){
            $status = 500;
            $result = ResponseJson::result(false,'Something went wrong!');
            return response()->json($result,$status);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    private function authenticate($credentials){
        if(auth()->attempt($credentials)){
            $user = auth()->user();
            $token = $user->createToken($user->email.'-'.now());
            if($token && $token->accessToken){
                $authenticatedUser = [
                    'token' => $token->accessToken,
                    'user' => $this->userWithCompanyType($user->id)
                ];
                //Mail::to($user)->queue(new UserRegistreted());
                $status = 200;
                $result = ResponseJson::result(true,'','user',$authenticatedUser);
            } else {
                $status = 500;
                $result = ResponseJson::result(false,'Something went wrong!');
            }
            return response()->json($result,$status);
        } else {
            $status = 401;
            $result = ResponseJson::result(false,'Unauthorized!');
            return response()->json($result,$status);
        }
    }

    public function userWithCompanyType($id){
        $userWithCompany = $this->user->where('id',$id)->with('companyType')->first();
        return $userWithCompany;
    }

    public function verifyEmail($email){
        $user = $this->user->where('email',$email)->first()->toArray();
        if(empty($user)){
            return FALSE;
        }
        return $user;
    }

    // public function sendResetPasswordMail($user){
    //     $encryptedEmailAddress = Crypt::encryptString($user['email']);
    //     $user['encryptedEmail'] = $encryptedEmailAddress;
    //     Mail::to((object) $user)->queue(new ResetPassowrdMail($user));
    // }

    public function getEmailAddress(Request $request){
        $email = $request->input('email');
        try {
            $decruyptedEmailAddress = Crypt::decryptString($email);
            $status = 200;
            $result = ResponseJson::result(true,'','email',$decruyptedEmailAddress);
            return response()->json($result,$status);
        } catch (DecryptException $e) {
            $status = 500;
            $result = ResponseJson::result(false,$e->getMessge());
            return response()->json($result,$status);
        } 
    }

    public function assembly(){
        $user = auth('api')->user();
        $assembly = $this->user->find($user->id);
        $assembly = $assembly->loadCount(
            [
                'clients' => function ($query) {
                    $query->where('role', 1);
        
                },
                'managers' => function ($query) {
                    $query->where('role', 2);
        
                },
                'developers' => function ($query) {
                    $query->where('role', 3);
        
                }
            ]
        );
        return ResponseJson::success('','assembly',$assembly);
    }
}
