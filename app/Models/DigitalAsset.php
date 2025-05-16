<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DigitalAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
        'is_minted',
        'minted_url',
        'mint_result'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
