<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'category_id',
        'tag_id',
        'status',
        'published_at',
        'views',
        'video_id',
        'video_type',
    ];

    //
    protected $casts = [
        'published_at' => 'datetime',
        'views' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
