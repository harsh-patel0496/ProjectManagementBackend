<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

use App\Facades\Material;
use App\Facades\ResponseJson;

class TaskController extends Controller
{

    protected $task;
    
    public function __construct(Task $task){
        $this->task = $task;
        $this->middleware("auth:api");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($project_id,$view = '')
    {
        $taskWithProjects = $this->taskWithProjects($project_id,$view);
        return ResponseJson::success('','tasks',$taskWithProjects);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $dataToSave = Material::TaskStore($request);
        $newTask = $this->task->create($dataToSave);
        $this->assignTaskToMAD($newTask,$request);
        $taskWithProject = $this->taskWithProjects($newTask['project_id']);
        return ResponseJson::success('','tasks',$taskWithProject);

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
        $data = $request->all();
        $task = $this->task->where('id',$id)->update(['type' => $data['destinationType']]);
        if($task == 1){
            
            foreach($data['newPriority'] as $key => $val){
                $this->task->where('id',$key)->update(['priority' => $val]);
            }
            return ResponseJson::success();
        } else {
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
        //
    }

    public function assignTaskToMAD($task,$request){
        $managers = $request->input('managers');
        $developers = $request->input('developers');

        if(!empty($developers)){
            foreach($developers as $key => $developer){
                $task->developers()->attach($developer['id']);
            }
        }

        if(!empty($managers)){
            foreach($managers as $key => $manager){
                $task->managers()->attach($manager['id']);
            }
        }
    }

    protected function taskWithProjects($project_id,$view = false){
        $taskWithProjects = $this->task->with('projects')->whereHas('projects' , function($query) use ($project_id){
            $query->where('projects.id',$project_id);
        })->orderBy('tasks.priority', 'desc')->get();

        //dd($taskWithUser);
        if($view == 'project'){
            return $taskWithProjects;
        } else {
            $finalArray = [];
            foreach($taskWithProjects as $task){
                $finalArray[$task->type][] = $task;
            }
            return $finalArray;
        }
        
    }
}
