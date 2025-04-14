<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'status',
        'published_at',
    ];


    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class);
    }

    /**
     * The posts that belong to the category.
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
