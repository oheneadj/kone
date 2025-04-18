<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateLink extends Model
{
    /** @use HasFactory<\Database\Factories\AffiliateLinkFactory> */
    use HasFactory;
    protected $fillable = [
        'product_id',
        'link',
        'label',
        'is_primary',
        'is_active',
        'clicks',
        'url_code',
    ];
    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'clicks' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function affiliateLinkData()
    {
        return $this->hasMany(AffiliateLinkData::class);
    }
}
