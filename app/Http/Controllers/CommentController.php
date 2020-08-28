<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\Project;

use App\Facades\Material;
use App\Facades\ResponseJson;

class CommentController extends Controller
{

    public $comment;
    
    public function __construct(Comment $comment){
        $this->comment = $comment;
        $this->middleware("auth:api");
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($project_id)
    {
        $parentUser = auth('api')->user()->parent_user;
        $companyId = $parentUser != NULL ? $parentUser : auth('api')->user()->id;
        $projectWithComment = Project::where('id',$project_id)
                                ->with(
                                    [
                                        'comments' => function($query){
                                            $query->with('users');
                                        }
                                    ]
                                )->first();
        return ResponseJson::success('','comments',$projectWithComment['comments']);
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
            $dataToSave = Material::CommentStore($request);
            $newComment = $this->comment->create($dataToSave);
            if($newComment){
                $this->attachCommentToTaskOrProject($newComment,$request);
            }

            return ResponseJson::success('','comment',$newComment);
        } catch(Exception $e){

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

    public function attachCommentToTaskOrProject($comment,$request){
        $data = $request->all();
        if(isset($data['project']) && !empty(($data['project']))){
            $comment->projects()->attach($data['project']['id']);
        }
        if(isset($data['task']) && !empty(($data['task']))){
            $comment->tasks()->attach($data['Task']['id']);
        }
    }
}
