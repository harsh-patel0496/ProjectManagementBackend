<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyType;
use App\Facades\ResponseJson;

class CompanyTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    protected $company_type;

    public function __construct(CompanyType $company_type){
        $this->company_type = $company_type;
        $this->middleware("auth:api",['except' => ['index']]);
    }

    public function index()
    {
        try{
            $company_types = $this->company_type->get();
            if(!empty($company_types)){
                $result = ResponseJson::result(true,'','companyTypes',$company_types);
                $status = 200;
            } else {
                $result = ResponseJson::result(false,'Something Went Wrong!');
                $status = 500;
            }
            return response()->json($result,$status);
        } catch(Exception $e){
            $result = ResponseJson::result(false,$e->getMessage());
            $status = 500;
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
