<?php

namespace App\Interfaces;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;

interface CommentRepositoryInterface
{
    public function create(array $data): Comment;

    public function delete(Comment $comment): bool;
}
