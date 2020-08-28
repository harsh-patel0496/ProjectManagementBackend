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
        $projects = $this->getProjectWithAssembly([],true);
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
            
            $project = $this->getProjectWithAssembly($newProject);
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
        try{
            $dataToSave = Material::ProjectStore($request);
            $updatedProject = tap($this->project->where('id',$id))->update($dataToSave)->first();
            if(!empty($updatedProject)){
                $this->syncWithProjects($updatedProject,$request);
            }
            $project = $this->getProjectWithAssembly($updatedProject);
            return ResponseJson::success('Project Updated Successfully!','project',$project);
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
                    ->with('developers')
                    ->with('managers')
                    ->orderBy('name','asc')
                    ->get();

        $assembly = ['clients' => $clients,'teams' => $teams];
        return ResponseJson::success('','assembly',$assembly);
    }

    public function syncWithProjects($project,$request){
        $teams = $request->input('teams');
        if(!empty($teams)){
            $toBeSyncTeams = [];
            foreach($teams as $key => $team){
                $toBeSyncTeams[] = $team['id'];
            }
            $project->teams()->sync($toBeSyncTeams);
        }
    }

    public function getProjectWithAssembly($newProject = [],$list = false){
        if($list){
            $projects = $this->project
                        ->where('company_id',auth('api')->user()->id)
                        ->with('client')
                        ->with(['teams' => function($query){
                            $query->withCount('developers')->withCount('managers');
                        }])
                        ->withCount('tasks')
                        ->orderBy('projects.id','desc')->get()->toArray();
            foreach($projects as $key => $project){
                $count = 0;
                if(!empty($project['teams'])){
                    foreach($project['teams'] as $k => $team){
                        $count = $count + $team['developers_count'] + $team['managers_count']; 
                    }
                }
                $projects[$key]['start_date'] = date('d-m-Y',strtotime($projects[$key]['start_date']));
                $projects[$key]['totalTeamMembers'] = $count;
                $totalTasks = $this->project->find($project['id'])->tasks()->count();
                $cancelledTasks = $this->project->find($project['id'])->tasks()->where('type',4)->count();
                $completedTasks = $this->project->find($project['id'])->tasks()->where('type',3)->count();
                if($totalTasks > 0){
                    $progress = ($completedTasks * 100)/($totalTasks - $cancelledTasks);
                } else {
                    $progress = 0;
                }
                
                $projects[$key]['progress'] = $progress;

            }

            return $projects;
        } else {
            $project = $this->project
                        ->where('projects.id',$newProject->id)
                        ->with('client')
                        ->with(['teams' => function($query){
                            $query->withCount('developers')->withCount('managers');
                        }])->first()->toArray();
            $count = 0;
            if(!empty($project['teams'])){
                foreach($project['teams'] as $k => $team){
                    $count = $count + $team['developers_count'] + $team['managers_count']; 
                }
            }
            $project['start_date'] = date('d-m-Y',strtotime($project['start_date']));
            $project['totalTeamMembers'] = $count;
            return $project;
        }
       
    }

    public function getProjectWithTaskForDashboard(){
        $companyId = auth('api')->user()->role == 0 ? auth('api')->user()->id : auth('api')->user()->parent_user;
        $projects = $this->project
                            ->where('company_id',$companyId)
                            ->get();
        $data = [];
        if(!empty($projects)){
            foreach($projects as $key => $project){
                $data['labels'][] =  $project->title;
                $data['datasets'][0][] = $project->tasks()->where('type',1)->count();
                $data['datasets'][1][] = $project->tasks()->where('type',2)->count();
                $data['datasets'][2][] = $project->tasks()->where('type',3)->count();
            }
            
        }
        return ResponseJson::success('','projects',$data);
    }
}
