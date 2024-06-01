<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;
    protected $table = 'asset_categories';
    protected $fillable = [
        'name',
        'description',
        'type',
        'parent_id',
        'status',

    ];
}
