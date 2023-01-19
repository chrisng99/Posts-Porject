<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'Anonymous',
        ]);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
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

    public function scopeFilterByCategories($query, $categoryFilters): void
    {
        foreach ($categoryFilters as $key => $categoriesFilter) {
            if ($key === array_key_first($categoryFilters)) {
                $query->where('category_id', $categoriesFilter);
            } else {
                $query->orWhere('category_id', $categoriesFilter);
            }
        }
    }

    public function scopeFilterByAuthor($query, $userId = null): void
    {
        $query->where('user_id', $userId);
    }
}
