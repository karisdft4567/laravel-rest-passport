<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Post;
use Validator;

class PostController extends Controller
{
    public $successStatus = 200;

    public function __construct(){
        $this->middleware(['auth:api'])->except('index', 'show');
        $this->middleware(['resourceModification'])->only('store', 'update', 'destroy');;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderby('id', 'desc')->with('users')->paginate(5);
        return response()->json(['posts' => $posts], $this-> successStatus); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validating title and body field
        $validator = Validator::make($request, [
            'user_id'=>'required|exists:users,id',
            'title'=>'required|max:100',
            'body' =>'required',
            ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $post = Post::create($request->only('user_id', 'title', 'body'));
        if($post){
            return response()->json(['post' => $post], $this-> successStatus); 
        }
        return response()->json(['error' => "Unable to create"], 401);        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return response()->json(['post'=>$post], $this-> successStatus); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
    public function update(Request $request, Post $post)
    {
        $validator = Validator::make($request->all(), [
            'user_id'=>'required|exists:users,id',
            'title'=>'required|max:100',
            'body' =>'required',
            ]);
        if ($validator->fails()) { 
            return response()->json(['error'=>$validator->errors()], 401);            
        }
        $input = $request->all();
        $post->title = $input['title'];
        $post->body = $input['body'];
        $post->status = $input['status'];
        $post->save();
        return response()->json(['post'=>$post], $this-> successStatus); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        $post->delete();
        return response()->json(['post'=>$post], $this-> successStatus); 
    }
}
