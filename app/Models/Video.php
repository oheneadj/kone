<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Video extends Model
{
    /** @use HasFactory<\Database\Factories\VideoFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'url',
        'thumbnail',
        'user_id',
        'status',
        'published_at',
        'is_featured',
        'views',
        'video_id',
        'video_type',
        'tag',
    ];

    //
    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
    ];

    /**
     * Get the user that owns the video.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Get the categories for the video.
     */
    public function categories(): BelongsToMany
    {
        return $this->BelongsToMany(Category::class);
    }
    /**
     * Get the tags for the video.
     */


    public function getRouteKeyName()
    {
        return 'video_id';
    }


    public static function boot()
    {
        parent::boot();

        //add user id to the video before saving

        static::creating(function ($video) {
            $video->user_id = auth()->id();
        });
        //add user id to the video before updating
        static::updating(function ($video) {
            $video->user_id = auth()->id();
        });
    }
}
