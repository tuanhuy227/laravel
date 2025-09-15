<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Post::with('images')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'author' => 'required|string|max:255',
             'published_at' => 'nullable|date',
             'images.*' => 'image'
        ]);

        $post = Post::create($data);
        if ($request->hasFile('images')){
            foreach($request->file('images') as $file ) {
                $path = $file->store('uploads', 'public');
                $post->images()->create(['path' => $path]);
            }
        }

        return response()->json($post->load('images'), 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return $post->load('images');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $$post)
    {
        $data = $request->validate([
            'title' => 'string|max:255',
            'content' => 'string',
            'author' => 'string',
            'published_at' => 'date'
        ]);

        $post->update($data);

        if ($request->hasFile('images')){
            foreach($request->file('images') as $image) {
                $path = $file->store('uploads', 'public');
                $post->images()->create(['path' => $path]);
            }
        }

        return response()->json($post->load('images'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        foreach ($post->images as $img) {
            \Storage::disk('public')->delete($img->path);
            $img->delete();
        } 
        $post->delete();
        return response()->json(null, 204);
    }
}
