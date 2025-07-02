<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StorePostRequest;
use App\Http\Requests\Api\UpdatePostRequest;
use App\Http\Resources\Api\PostResource;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(private PostService $postService) {}

    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 10);
        $posts = $this->postService->getPaginatedPublished($search, $perPage, ['categories']);
        return PostResource::collection($posts);
    }

    public function show($id)
    {
        $post = Post::with('categories')
            ->where('status', 'published')
            ->where(fn($q) => $q->where('id', $id)->orWhere('slug', $id))
            ->firstOrFail();

        return new PostResource($post);
    }

    public function store(StorePostRequest $request)
    {
        $post = $this->postService->create($request->validated());
        return new PostResource($post);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {

        $this->postService->update($post, $request->validated());
        return new PostResource($post);
    }

    public function destroy(Post $post)
    {
        $this->postService->delete($post);
        return response()->json(['message' => 'Post deleted']);
    }
}
