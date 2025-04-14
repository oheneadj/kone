<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'tag_id',
    ];

    protected $casts = [
        'tag_id' => 'integer',
    ];

    public function videos()
    {
        return $this->hasMany(Video::class);
    }
}
