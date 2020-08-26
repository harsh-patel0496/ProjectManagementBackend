<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Employee;
use App\Team;

use App\Facades\Material;
use App\Facades\ResponseJson;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $project;
    
    public function __construct(Project $project){
        $this->project = $project;
        $this->middleware("auth:api");
    }
    
    public function index()
    {
        $projects = $this->project
                        ->where('company_id',auth('api')->user()->id)
                        ->with('client')
                        ->with(['teams' => function($query){
                            $query->withCount('developers');
                        }])->orderBy('projects.id','desc')->get()->toArray();
        //echo"<pre>";print_r($projects);
        foreach($projects as $key => $project){
            $count = 0;
            if(!empty($project['teams'])){
                foreach($project['teams'] as $k => $team){
                    $count = $count + $team['developers_count']; 
                }
            }
            $projects[$key]['start_date'] = date('d-m-Y',strtotime($projects[$key]['start_date']));
            $projects[$key]['totalDevelopers'] = $count;
        }
        return ResponseJson::success('','projects',$projects);
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
            $teams = $request->input('teams');
            $dataToSave = Material::ProjectStore($request);
            $newProject = $this->project->create($dataToSave);
    
            if(!empty($newProject)){
                $this->assignProjectToTeam($newProject,$teams);
            }
            $project = $this->project
                        ->where('projects.id',$newProject->id)
                        ->with('client')
                        ->with(['teams' => function($query){
                            $query->withCount('developers');
                        }])->first()->toArray();
            $count = 0;
            if(!empty($project['teams'])){
                foreach($project['teams'] as $k => $team){
                    $count = $count + $team['developers_count']; 
                }
            }
            $project['start_date'] = date('d-m-Y',strtotime($project['start_date']));
            $project['totalDevelopers'] = $count;
            return ResponseJson::success('','project',$project);
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

    public function assignProjectToTeam($project,$teams){
        if(!empty($teams)){
            foreach($teams as $key => $team){
                $project->teams()->attach($team['id']);
            }
        }
    }

    public function getListOfTeamsAndClients(){
        $user = auth('api')->user()->id;
        $clients = Employee::havingRole(1)
                    ->select('id','name','email','role','parent_user')
                    ->where('parent_user',$user)
                    ->orderBy('name','asc')
                    ->get();

        $teams = Team::where('company_id',$user)
                    ->select('id','name','company_id')
                    ->orderBy('name','asc')
                    ->get();

        $assembly = ['clients' => $clients,'teams' => $teams];
        return ResponseJson::success('','assembly',$assembly);
    }
}
