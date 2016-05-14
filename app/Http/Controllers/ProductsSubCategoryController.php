<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \DB;
use \ORG;
use \Datatables;

class ProductsSubCategoryController extends Controller
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
                array( 'db' => 'sub_categories.id', 'dt' => 0 ),
                array( 'db' => 'sub_category_name',  'dt' => 1 ),
                array( 'db' => 'categories.category_name',  'dt' => 2 ),
                array( 'db' => 'sub_categories.description','dt' => 3),
                array( 'db' => 'sub_categories.date','dt' => 4,
                        'formatter'=>function($d,$row){
                            return date(ORG\Dates::DSPDATEFORMAT,strtotime($d));
                        }      
                    )
            );

            return Datatables\SSP::simple( $_GET, "sub_categories", "id", $columns,"join categories on categories.id = sub_categories.category_id");
        } 

        return \View::make("/Pos/SubCategoryList");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $cats = DB::select("SELECT id,category_name FROM categories");
        return \View::make("Pos/NewSubCategory",["cats"=>$cats]);
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
            'subcategory_name' => 'required|min:2',
            'category'=>"required"
        ]);

        if($data['category']==0){
            array_push($errors, "Please Choose Category");
        }

         if ($validator->fails()) {
            $errors  = $validator->errors();
         }

         if(count($errors)==0){
            $saved  = \DB::insert("insert into sub_categories (sub_category_name,category_id,description,date) values(?,?,?,?)",[$data['subcategory_name'],$data['category'],$data['description'],date(\ORG\Dates::DBDATEFORMAT) ]);
         }

        if($saved){
            $message = "Sub Category Saved";
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

    public function ajaxGetSubCategories()
    {
        if(isset($_GET['id'])){
            $category_id = $_GET['id'];
        return json_encode(DB::select("select sub_category_name,id from sub_categories where category_id=?",[$category_id]));
        }
    }
}
