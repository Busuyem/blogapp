<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use App\Interfaces\CommentRepositoryInterface;

class CommentController extends Controller
{
    private CommentRepositoryInterface $commentRepository;

    public function __construct(CommentRepositoryInterface $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function store(Request $request, $postId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'comment' => 'required|string|max:1000',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $post = Post::find($postId);

            if (!$post) {
                return response()->json(['error' => 'Post not found'], 404);
            }

            $comment = $this->commentRepository->create([
                'post_id' => $post->id,
                'user_id' => Auth::check() ? Auth::id() : null, // allow guests
                'comment' => $request->comment,
            ]);

            return response()->json($comment, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to add comment', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(Comment $comment)
    {
        try {

            if (Gate::denies('delete', $comment)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $user = Auth::user();

            if (!$user || ($comment->user_id !== $user->id && $comment->post->user_id !== $user->id)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $this->commentRepository->delete($comment);

            return response()->json(['message' => 'Comment deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete comment', 'message' => $e->getMessage()], 500);
        }
    }
}
