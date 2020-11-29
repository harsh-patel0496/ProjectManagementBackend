<?php

namespace App\Http\Controllers;

use App\Item;
use Illuminate\Http\Request;
//use Illuminate\Support\Facades\Http;

use GuzzleHttp\Client;

use App\Facades\ResponseJson;

class ItemController extends Controller
{

    protected $item;
    protected $http;

    public function __construct(Item $item){
        $this->item = $item;
        $this->http = new Client();
        $this->middleware("auth:api");
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = $this->item->get();
        return ResponseJson::success('','items',$items);
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $newItem = $this->item->create($request->all());
        return ResponseJson::success('','item',$newItem);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = $this->item->find($id);
        if(!empty($item)){
            $res = $this->http->request('GET','http://127.0.0.1:3001/Scrapping',[
                    'query' => ['url' => $item->url]
                ]);
            return response($res->getBody());
        } else {
            return ResponseJson::error();
        }
        
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
