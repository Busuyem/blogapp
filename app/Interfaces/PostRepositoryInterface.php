<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Post;

interface PostRepositoryInterface
{
    public function getAll(): Collection;

    public function getById(int $id): ?Post;

    public function create(array $data): Post;

    public function update(Post $post, array $data): bool;

    public function delete(Post $post): bool;
}
