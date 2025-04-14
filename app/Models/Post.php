<?php

namespace App\Models;

use Illuminate\Support\Str;
use Doctrine\DBAL\Schema\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;
    protected $fillable = [
        'title',
        'featured_image',
        'content',
        'slug',
        'user_id',
        'tags',
        'status',
        'views',
        'published_at',
    ];

    //Cast datetime to Carbon instance
    protected $casts = [
        'published_at' => 'datetime',
        'tags' => 'array',
    ];

    // Define the relationship with the User model
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with the Category model
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    //add auth user to the post before saving
    public static function boot()
    {
        parent::boot();

        //add user id to the post before saving

        static::creating(function ($post) {
            $post->user_id = auth()->id();
        });
        //add user id to the post before updating
        static::updating(function ($post) {
            $post->user_id = auth()->id();
        });

        //create a slug from the title before saving
        static::creating(function ($post) {

            $post->slug = Str::slug($post->title);
        });


        //create a slug from the title before updating
        static::updating(function ($post) {
            $post->slug = Str::slug($post->title);
        });
        //create a slug from the title before saving

    }
}
