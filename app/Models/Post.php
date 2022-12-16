<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'post_text', 'category_id', 'user_id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function postTextTruncated(): Attribute
    {
        return Attribute::make(
            get: fn () => Str::limit($this->post_text, 150, '...'),
        );
    }

    public function scopeIsAuthor($query): void
    {
        $query->where('user_id', auth()->id());
    }

    public function scopeSearch($query, $search): void
    {
        $query->where('title', 'like', '%' . $search . '%')
            ->orWhere('post_text', 'like', '%' . $search . '%');
    }

    public function scopeFilterByCategories($query, $categoriesFilters): void
    {
        foreach ($categoriesFilters as $key => $categoriesFilter) {
            if ($key === array_key_first($categoriesFilters)) {
                $query->where('category_id', $categoriesFilter);
            } else {
                $query->orWhere('category_id', $categoriesFilter);
            }
        }
    }
}
