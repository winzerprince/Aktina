<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'user_id',
        'resources',
    ];

    protected $casts = [
        'resources' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }
}
