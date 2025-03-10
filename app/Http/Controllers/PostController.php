<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PostDetailResource;

class PostController extends Controller
{
    public function index(){
        $posts = Post::all();  

        // api resource bisa mengubah file yang dikirimkan ke FE
        return PostDetailResource::collection($posts->loadMissing('writer:id,username', 'comments:id,post_id,user_id,comments_content'));     //pakai collection untuk syarat array. loadmissing sama dgn with. bedanya penggunaan with pada tmpt eloquent, loadmissing setlah variable eloq
    }

    public function show($id)
    {
        $post = Post::with('writer:id,username')->findOrFail($id);
        return new PostDetailResource($post->loadMissing('writer:id,username', 'comments:id,post_id,user_id,comments_content'));    //untuk 1/tidak untuk array

    }
    // public function show2($id)
    // {
    //     $post = Post::findOrFail($id);
    //     return new PostDetailResource($post);    //untuk 1/tidak untuk array

    // }

    public function store(Request $request){

        $validated = $request->validate([
            'title'=>   'required|max:255',
            'news_content'=> 'required',
        ]);

        $request['author'] = Auth::user()->id;
        $post = Post::create($request->all());
        return new PostDetailResource($post->loadMissing('writer:id,username'));    

    }

    public function update(Request $request, $id){
        
        $validated = $request->validate([
            'title'=>   'required|max:255',
            'news_content'=> 'required',
        ]);

        $post = Post::findOrFail($id);
        $post->update($request->all());

        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function destroy($id){

        $post = Post::findOrFail($id);
        $post->delete();

        return new PostDetailResource($post->loadMissing('writer:id,username'));

    }

}
