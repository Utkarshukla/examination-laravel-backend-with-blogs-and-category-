<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Category;
use App\Models\PostRelationCategory;
use App\Models\PostRelationTag;


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
            'media.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,png,jpeg,jpg,gif|max:10240',
            'categories.*.name' => 'required',
            'tags.*.name' => 'required',
        ]);
        
echo $request->thumbnail;
echo $request->media;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailPath = $thumbnail->store('thumbnails', 'public');
            $validatedData['thumbnail'] = $thumbnailPath;
        }
        
        if ($request->hasFile('media')) {
            $mediaPaths = [];
            foreach ($request->file('media') as $file) {
                $mediaPath = $file->store('media', 'public');
                $mediaPaths[] = $mediaPath;
            }
            $validatedData['media'] = json_encode($mediaPaths);
        }
        
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

        return response()->json(['message' => 'Post created successfully'], 201);
    }

    public function showAll(){
        $posts = Post::with('tags', 'categories')->paginate(5);
        return response()->json($posts);
    }
    public function show($id)
    {
        
        $post = Post::with('tags', 'categories')->findOrFail($id);

        // Decode the media field from JSON string to array
        $post->media = json_decode($post->media);
    
        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        // Similar to store method but for updating existing post
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return response()->json(['message' => 'Post deleted successfully']);
    }
}
