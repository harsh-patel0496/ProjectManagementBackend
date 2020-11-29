<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use App\Team;
use App\Employee;
use App\User;

use App\Facades\ResponseJson;
use App\Facades\Material;

class TeamController extends Controller
{
   

    protected $team;
    
    public function __construct(Team $team){
        $this->team = $team;
        $this->middleware("auth:api",['except' => ['getListOfEmployee']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $limit = request()->query('per_page');
        $page = request()->query('page');
        
        $teams = $this->team->where(
                    'company_id',auth('api')->user()->id
                )->with(
                    ['developers','managers']
                )->orderBy('id','desc')->paginate($limit, ['*'], 'page', $page);
        return ResponseJson::success('','teams',$teams);
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
            $dataToSave = Material::TeamStore($request);
            $newTeam = $this->team->create($dataToSave);
            if(!empty($newTeam)){
                $this->attachDevelopersAndManagers($newTeam,$request);
            }
            return ResponseJson::success('Team Created Successfully!','team',$newTeam);
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
            $dataToSave = Material::TeamStore($request);
            $updatedTeam = tap($this->team->where('id',$id))->update($dataToSave)->first();
            if(!empty($updatedTeam)){
                $this->syncWithTeams($updatedTeam,$request);
            }
            return ResponseJson::success('Team Updated Successfully!','team',$updatedTeam);
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
            $destroy = $this->team->destroy($id);
            return ResponseJson::success('Team Deleted Successfully!','team',$destroy);
        } catch(Exception $e){
            return ResponseJson::error();
        }
    }

    public function validateTeamName(Request $request){
        
        $name = $request->input('name');
        if($name != ''){
            if($request->input('edit')){
                $validator = Validator::make($request->all(), [
                    "name" => [
                        'required',
                        Rule::unique('teams')->ignore($request->input('id')),
                    ]
                ]);
            } else {
                $validator = Validator::make($request->all(), [
                    "name" => 'unique:teams|string'
                ]);
            }
            
            
            if ($validator->fails()) {
                return ResponseJson::error('The name has already been taken.',401);
            } 
        }
        
        return ResponseJson::success();
    }

    public function getListOfEmployee(){
        $managers = Employee::havingRole(2)->where(
                'parent_user',auth('api')->user()->id
            )->orderBy('name','asc')->get();
        $developers = Employee::havingRole(3)->where(
            'parent_user',auth('api')->user()->id
        )->orderBy('name','asc')->get();

        $employees = ['managers' => $managers,'developers' => $developers];
        return ResponseJson::success('','employees',$employees);
    }

    public function attachDevelopersAndManagers($team,$request){

        $managers = $request->input('managers');
        $developers = $request->input('developers');
        $primeUser = auth('api')->user();
        if($primeUser->role == 0){
            $primeUser->chatGroups()->attach($team->id,['chated_at' => date('Y:m:d H:i:s'),'accept_invite' => 1]);
        }
        if(!empty($managers)){
            foreach($managers as $key => $manager){
                User::find($manager['id'])->chatGroups()->attach($team->id,['chated_at' => date('Y:m:d H:i:s'),'accept_invite' => 1]);
                $team->managers()->attach($manager['id']);
            }
        }
        if(!empty($developers)){
            foreach($developers as $key => $developer){
                User::find($developer['id'])->chatGroups()->attach($team->id,['chated_at' => date('Y:m:d H:i:s'),'accept_invite' => 1]);
                $team->developers()->attach($developer['id']);
            }
        }
    }

    public function syncWithTeams($team,$request){
        $managers = $request->input('managers');
        $developers = $request->input('developers');
        if(!empty($managers)){
            $toBeSyncManagers = [];
            foreach($managers as $key => $manager){
                $toBeSyncManagers[] = $manager['id'];
            }
            $team->managers()->sync($toBeSyncManagers);
        }
        if(!empty($developers)){
            $toBeSyncDevelopers = [];
            foreach($developers as $key => $developer){
                $toBeSyncDevelopers[] = $developer['id'];
            }
            $team->developers()->sync($toBeSyncDevelopers);
        }
    }

    public function detachEmployee(Request $request){
        $data = $request->all();
        $team = $this->team->find($data['team']);
        if($data['role'] == 2){
            $detached = $team->managers()->detach($data['id']);
            dd($detached);
        } else {
            $team->developers()->detach($data['id']);
        }
    }
}
