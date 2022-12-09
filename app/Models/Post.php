<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'post_text', 'category_id', 'user_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPostTextTruncatedAttribute()
    {
        return Str::limit($this->post_text, 150, '...');
    }

    public function scopeIsAuthor($query)
    {
        $query->where('user_id', auth()->id());
    }
}