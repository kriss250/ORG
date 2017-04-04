<?php

/**
 * OperationsController short summary.
 *
 * OperationsController description.
 *
 * @version 1.0
 * @author kris
 */
namespace Kris\HR\Controllers;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function store()
    {
        $data = \Request::all();

        if(isset($data['_post']) && $data['_post'] > 0)
        {
            //Update
            $post = \Kris\HR\Models\Post::find($data['_post']);
            $post->name =  $data['name'];
            $post->description =$data['description'];
            $post->save();
            return redirect()->back();
        }else {
            //Create
            $created = \Kris\HR\Models\Post::create([
                "name"=>$data['name'],
                "description"=>$data['description'],
                "department_id"=>$data['department'],
                "deleted"=>0
                ]);
            return $created != null ? redirect()->back()->with("msg","Post Successfuly created") : redirect()->back()->withErrors(["Error creating Post"]);
        }
    }

    public function delete()
    {

    }

    public static function getPosts($dp)
    {
        return \Kris\HR\Models\Post::where("department_id",$dp)->get();
    }

    public function edit($id)
    {
        return \View::make("HR::newPost",["post"=>\Kris\HR\Models\Post::find($id)]);
    }
}
