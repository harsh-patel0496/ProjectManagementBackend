<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Message;
use App\User;
use App\Events\RealTimeChatEvent;

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
    public function index($active_chat,$isTeam = false)
    {
        $messages = $this->messagesForActiveChat($active_chat);
        return ResponseJson::success('','messages',$messages);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dataToSave = Material::MessageStore($request);
        $newMessage = $this->message->create($dataToSave);
        //$newMessage->friends()->attach($request->input('send_to'));
        
        $this->fireMesssage($newMessage);
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

    protected function messagesForActiveChat($active_chat,$isTeam = false){

        $messages = [];
        $user = auth('api')->user();
        $join = 'friends';
        $condition = 'users';

        if($isTeam){
            $join = 'teams';
            $condition = $join;
        }

        $messages = $this->message->
                        /*->with([$join => function($query) use ($active_chat,$condition){
                            $query->where($condition.'.id',$active_chat);
                        }])*/
                        where([
                            ['sender_id','=',$user->id],
                            ['receiver_id','=',$active_chat]
                        ])
                        ->orWhere([
                            ['sender_id','=',$active_chat],
                            ['receiver_id','=',$user->id]
                        ])
                        ->get();

        return $messages;
    }

    public function fireMesssage($message){
        
        if(!empty($message)){
            $channel = (auth('api')->user()->id * $message->receiver_id);
            event(new RealTimeChatEvent($message,'App.User.'.$channel));
        }
    
    }

    public function inviteFriendsForChat(Request $request){

        try{

            $friend = User::find($request->input('friend_id'));
            
            if(!empty($friend)){
                
                return $this->createNewChat($friend->id);
                
            } else {
                return ResponseJson::error();
            }

        } catch(Exception $e){
            
            return ResponseJson::error();
        
        }
        
    }

    public function createNewChat($friend_id){

        try{
                
            auth('api')->user()->friends()->attach($friend_id,['chated_at' => date('Y:m:d H:i:s')]);
                
            return ResponseJson::success('Request has been sent!');
        
        } catch(Exception $e){
            
            return ResponseJson::error();
        
        }
    }

    public function acceptInvite($friend_id){

        try{
                
            auth('api')->user()->friends()->sync([
                $friend_id => [
                    'chated_at' => date('Y:m:d H:i:s'),
                    'accept_invite' => 1
                ]
            ]);
                
            return ResponseJson::success('Request has been accepted!');
        
        } catch(Exception $e){
            
            return ResponseJson::error();
        
        }
    }

    public function getFriendList(){
        $user = auth('api')->user();

        $friends = $user->friends()->select('users.*','friendables.*')->orderBy('friendables.chated_at','desc')->get()->toArray();
        $groups = $user->chatGroups()->select('teams.*','friendables.*')->orderBy('friendables.chated_at','desc')->get()->toArray();
        $data = array_merge($friends,$groups);
        $dateSort = array_column($data, "chated_at");
        array_multisort($dateSort, SORT_DESC, $data);
        return ResponseJson::success('','friends',$data);   
    }
}
