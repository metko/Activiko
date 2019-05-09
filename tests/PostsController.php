<?php

namespace Metko\Activiko\Tests;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PostsController extends Controller
{
    public function store(Request $request)
    {
        Post::create($request->all());
    }

    public function update($post_id, Request $request)
    {
        $post = Post::findOrFail($post_id);
        $post->update(['name' => $request->name]);
    }

    public function destroy($post_id)
    {
        $post = Post::findOrFail($post_id);
        $post->delete();
    }
}
