<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Type extends Model
{
    /** @use HasFactory<\Database\Factories\TypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type_id',
    ];

    public function related_types(): HasMany
    {
        return $this->hasMany(Type::class);
    }

    public function provider(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Provider::class);
    }

    /**
     * Get the route key name for the model.
     *
     * @return string
     */

    public function product(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }
}
