<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\PostRepositoryInterface;

class PostController extends Controller
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function index()
    {
        try {
            $posts = $this->postRepository->getAll();
            return response()->json($posts, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch posts', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $post = $this->postRepository->create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'content' => $request->content,
            ]);

            return response()->json($post, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create post', 'message' => $e->getMessage()], 500);
        }
    }

   public function show($id)
    {
        try {
            $post = $this->postRepository->getById($id);

            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }

            return response()->json($post, 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch post', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Post $post)
    {
        try {
            if (Gate::denies('update', $post)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            if ($post->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $this->postRepository->update($post, $request->only(['title', 'content']));

            return response()->json(['message' => 'Post updated successfully', "post" => $post], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update post', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Post $post)
    {
        try {
           
             if (Gate::denies('delete', $post)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if ($post->user_id !== Auth::id()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $this->postRepository->delete($post);

            return response()->json(['message' => 'Post deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete post', 'message' => $e->getMessage()], 500);
        }
    }
}
