<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Message;

use App\Facades\Material;
use App\Facades\ResponseJson;

class MessageController extends Controller
{

    protected $message;

    public function __construct(Message $message){
        $this->message = $message;
        $this->middleware("auth:api");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($this->message);
        $dataToSave = Material::MessageStore($request);
        $newMessage = $this->message->create($dataToSave);

        $newMessage->friends()->attach($request->input('send_to'));
        return ResponseJson::success('','newMessage',$newMessage);
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
        //
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
}
