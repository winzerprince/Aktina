<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionManager extends Model
{
    use HasFactory;

    protected $table = 'production_manager';

    protected $guarded = [
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
