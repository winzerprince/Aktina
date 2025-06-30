<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'generated_at' => 'datetime',
    ];

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
