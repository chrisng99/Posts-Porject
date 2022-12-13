<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Post $post): Response|bool
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): Response|bool
    {
        return $user->id === $post->user_id;
    }
}