<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use \Datatables;
use \ORG;
class ProductsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
               if(isset($_GET['json'])){

            $columns = array(
                array( 'db' => 'id', 'dt' => 0 ),
                array( 'db' => 'category_name',  'dt' => 1 ),
                array( 'db' => 'description','dt' => 2),
                array( 'db' => 'date','dt' => 3,
                        'formatter'=>function($d,$row){
                            return date(ORG\Dates::DSPDATEFORMAT,strtotime($d));
                        }      
                    )
            );

            return Datatables\SSP::simple( $_GET, "categories", "id", $columns,"");
            
            
        } 

        return \View::make("/Pos/CategoryList");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $stores = \DB::select("select * from store");
        return \View::make("Pos/NewCategory",["stores"=>$stores]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $req)
    {
        
        $saved = false;
        $errors  = array();
        $message = "";
        $data = $req->all();
        $validator = \Validator::make($req->all(), [
            'category_name' => 'required|min:2',
            'store'=> 'required'
        ]);

        if($req->input("store")==0)
        {
            array_push($errors, "Please Choose Store");
        }

         if ($validator->fails()) {
            $errors  = $validator->errors();
         }

         if(count($errors)==0){
            $saved  = \DB::insert("insert into categories (category_name,description,store_id,date) values(?,?,?,?)",[$data['category_name'],$data['description'],$data['store'],date(\ORG\Dates::DBDATEFORMAT) ]);
         }

        if($saved){
            $message = "Category Saved";
        }

        $response = array(
            'message' => $message, 
            'errors'=> $errors
        );

        return json_encode($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
