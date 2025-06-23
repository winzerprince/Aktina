<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $table = 'resource';

    protected $fillable = [
        'name',
        'units',
        'reorder_level',
        'overstock_level',
        'description',
        'supplier_id',
        'bom_id',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function bom()
    {
        return $this->belongsTo(Bom::class);
    }

    public function isLowStock()
    {
        return $this->units <= $this->reorder_level;
    }

    public function isOverstock()
    {
        return $this->units >= $this->overstock_level;
    }
}
