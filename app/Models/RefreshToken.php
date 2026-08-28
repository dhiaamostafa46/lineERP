<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class RefreshToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'token', 'revoked', 'expires_at',
    ];

    protected $casts = [
        'revoked' => 'boolean',
        'expires_at' => 'datetime',
    ];
}
