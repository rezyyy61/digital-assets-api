<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MintRequest extends Model
{
    protected $fillable = [
        'digital_asset_id',
        'status',
        'digest',
        'error_message',
    ];

    public function digitalAsset(): BelongsTo
    {
        return $this->belongsTo(DigitalAsset::class);
    }
}
