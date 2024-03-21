<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Category;
use App\Models\PostRelationCategory;
use App\Models\PostRelationTag;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    public function store(Request $request)
    {
       

        $validatedData = $request->validate([
            'title' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'author' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            //'media.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpeg,jpg,gif|max:10240',
            'categories.*.name' => 'required',
            'tags.*.name' => 'required',
        ]);
        
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailPath = $thumbnail->store('thumbnails', 'public');
            $validatedData['thumbnail'] = $thumbnailPath;
        }

        // if ($request->hasFile('media')) {
        //     $mediaPaths = [];
        //     foreach ($request->file('media') as $file) {
        //         $mediaPath = $file->store('media', 'public');
        //         $mediaPaths[] = $mediaPath;
        //     }
        //     $validatedData['media'] = json_encode($mediaPaths);
        // }
        
        $post = Post::create($validatedData);
       
        $categoriesData = $request->input('categories');
        
        foreach ($categoriesData as $categoryData) {
            $category = Category::firstOrCreate(['name' => $categoryData['name']], $categoryData);
            PostRelationCategory::create(['post_id' => $post->id, 'category_id' => $category->id]);
        }

        $tagsData = $request->input('tags');

        foreach ($tagsData as $tagData) {
            $tag = Tag::firstOrCreate(['name' => $tagData['name']], $tagData);
            PostRelationTag::create(['post_id' => $post->id, 'tag_id' => $tag->id]);
        }

        return response()->json(['status' => 'success','message' => 'Post created successfully'], 201);
    }

    public function showAll(){
        $posts = Post::with('tags', 'categories')->paginate(5);
        return response()->json($posts);
    }
    public function show($id)
    {
        
        $post = Post::with('tags', 'categories')->findOrFail($id);

        $post->media = json_decode($post->media);
    
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id); //try catch not needed

        $validatedData = $request->validate([
            'title' => 'required',
            'short_description' => 'required',
            'long_description' => 'required',
            'author' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'categories.*.name' => 'required',
            'tags.*.name' => 'required',
        ]);
    
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailPath = $thumbnail->store('thumbnails', 'public');
            $validatedData['thumbnail'] = $thumbnailPath;
            Storage::disk('public')->delete($post->thumbnail);
        }
        $post->update($validatedData);
        $categoriesData = $request->input('categories');
        $post->categories()->detach(); 
        foreach ($categoriesData as $categoryData) {
            $category = Category::firstOrCreate(['name' => $categoryData['name']], $categoryData);
            $post->categories()->attach($category->id);
        }
        $tagsData = $request->input('tags');
        $post->tags()->detach(); 
        foreach ($tagsData as $tagData) {
            $tag = Tag::firstOrCreate(['name' => $tagData['name']], $tagData);
            $post->tags()->attach($tag->id);
        }
    
        return response()->json(['status' => 'success','message' => 'Post updated successfully'], 200);
    }
    
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json(['status' => 'success','message' => 'Post deleted successfully']);
    }
}
