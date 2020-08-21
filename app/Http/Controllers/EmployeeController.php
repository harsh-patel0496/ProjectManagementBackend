<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Managers;
use App\Developer;
use App\Facades\Material;
use App\Facades\ResponseJson;

class EmployeeController extends Controller
{
    
    private $employee;

    public function __construct(Employee $employee){
        $this->employee = $employee;
        $this->middleware("auth:api");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $role = request()->query('role');
        $limit = request()->query('per_page');
        $page = request()->query('page');
        $employees = $this->employee->havingRole($role)->where('parent_user',auth('api')->user()->id)->orderBy('id','desc')->paginate($limit, ['*'], 'page', $page);
        if(!empty($employees)){
            return ResponseJson::success('','employees',$employees);
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
            $dataToSave = Material::UserStore($request);
            $newUser = $this->employee->create($dataToSave)->toArray();
            $newUser['invite'] = true;
            $newUser['sender'] = auth('api')->user();
            $this->sendResetPasswordMail($newUser);
            return ResponseJson::success('','employee',$newUser);
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
            $updatedUser = tap($this->employee->where('id',$id))->update($request->all())->first();
            return ResponseJson::success('','employee',$updatedUser);
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
            $check = $this->checkAssigned($id);
            if($check){
                return ResponseJson::success("Can not perform this operation for the employee assigned to any Team!",'deleted',0);
            } else {
                $destroy = $this->employee->destroy($id);
                return ResponseJson::success('Employee Deleted Successfully!','employee',$destroy);
            }

            
        } catch(Exception $e){
            return ResponseJson::error();
        }
    }

    public function checkAssigned($id){
        $employee = $this->employee->find($id);
        if(!empty($employee)){
            if($employee->role == 2){
                $manager = Managers::where('id',$id)->with('teams')->first()->toArray();
                if(!empty($manager['teams'])){
                    return TRUE;
                }
            }
            if($employee->role == 3){
                $developer = Developer::where('id',$id)->with('teams')->first()->toArray();
                if(!empty($developer['teams'])){
                    return TRUE;
                }
            }
        }
        return FALSE;
    }
}
