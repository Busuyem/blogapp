<?php

namespace App\Repositories;

use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function getAll(): Collection
    {
        return Post::with([
            'user:id,name,email',
            'comments' => function ($query) {
                $query->latest();
            },
            'comments.user:id,name,email'
        ])->latest()->get();
    }


    public function getById(int $id): ?Post
    {
        return Post::with([
            'user:id,name,email',
            'comments' => function ($query) {
                $query->latest();
            },
            'comments.user:id,name,email'
        ])->find($id);
    }

    public function create(array $data): Post
    {
        return Post::create($data);
    }

    public function update(Post $post, array $data): bool
    {
        return $post->update($data);
    }

    public function delete(Post $post): bool
    {
        return $post->delete();
    }
}
