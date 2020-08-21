<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\Facades\Material;
use App\Facades\ResponseJson;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private $client;

    public function __construct(Client $client){
        $this->client = $client;
        $this->middleware("auth:api");
    }

    public function index()
    {
        $limit = request()->query('per_page');
        $page = request()->query('page');
        $clients = $this->client->role()->where('parent_user',auth('api')->user()->id)->orderBy('id','desc')->paginate($limit, ['*'], 'page', $page);
        if(!empty($clients)){
            return ResponseJson::success('','clients',$clients);
        } else {
            return ResponseJson::error();
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
        try{
            
            $dataToSave = Material::UserStore($request, 1);
            $dataToSave['role'] = 1; //Client
            $newUser = $this->client->create($dataToSave)->toArray();
            $newUser['invite'] = true;
            $newUser['sender'] = auth('api')->user();
            $this->sendResetPasswordMail($newUser);
            return ResponseJson::success('','client',$newUser);
        } catch(Excention $e){
            return ResponseJson::error();
        }
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
        try{
            $updatedUser = tap($this->client->where('id',$id))->update($request->all())->first();
            return ResponseJson::success('','client',$request->all());
        } catch(Excention $e){
            return ResponseJson::error();
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
        try{
            $destroy = $this->client->destroy($id);
            return ResponseJson::success('Client Deleted Successfully!','client',$destroy);
        } catch(Exception $e){
            return ResponseJson::error();
        }
    }

    
}
