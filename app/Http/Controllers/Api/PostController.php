<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::all();

        // return response()->json([
        //     'status' => true,
        //     'data' => $data,
        //     'message' => 'All Post Data',
        // ], 200);

        return $this->sendResponse($data, 'All Post Data');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valiteUser = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required | mimes:png,jpg,jpeg,gif',
            ]
        );

        if($valiteUser->fails()){
            
            // return response()->json([
            //     'status' => false,
            //     'message' => 'Validation Error',
            //     'errors' => $valiteUser->errors()->all(),
            // ], 401);

            return $this->sendError('Validation Error', $valiteUser->errors()->all());
        }

        $img = $request->image;
        $ext = $img->getClientOriginalExtension();
        $imageName = time() . "." . $ext;
        $img->move(public_path(). '/uploads', $imageName);

        $post = Post::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Post Created Successfully!',
            'user' => $post,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['posts'] = Post::select(
            'id',
            'title',
            'description',
            'image',
        )->where(['id' => $id])->get();

        return response()->json([
            'status' => true,
            'data' => $data,
        ], 200);
        
        return response()->json([
            'status' => true,
            'message' => 'Data get Successfully!',
            'user' => $data,
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $valiteUser = Validator::make(
            $request->all(),
            [
                'title' => 'required',
                'description' => 'required',
                'image' => 'required | mimes:png,jpg,jpeg,gif',
            ]
        );

        if($valiteUser->fails()){
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $valiteUser->errors()->all(),
            ], 401);
        }

        $postImage = Post::select('id', 'image')->where(["id" => $id])->get();

        if( $request->image != '' ){
            $path = public_path(). '/uploads';

            if($postImage[0]->image != '' && $postImage[0]->image != null){
                $old_file = $path. $postImage[0]->image;

                if(file_exists($old_file)){
                    unlink($old_file);
                }

            }

            $img = $request->image;
            $ext = $img->getClientOriginalExtension();
            $imageName = time() . "." . $ext;
            $img->move(public_path(). '/uploads', $imageName);
            
        }
        else{
            $imageName = $postImage->image;
        }


        $post = Post::where(['id' => $id])->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Post Updated Successfully!',
            'user' => $post,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $imageName = Post::select('image')->where('id', $id)->get();
        $filePath = public_path(). '/uploads/'. $imageName[0]['image'];
        unlink($filePath); // method to remove image from file
        $post = Post::where('id', $id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Post Delete Successfully!',
            'user' => $post,
        ], 200);

    }
}
