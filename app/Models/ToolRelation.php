<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolRelation extends Model
{
    use HasFactory;
    protected $table = 'tool_relation';
    protected $fillable = [
        'parent_id',
        'child_id'
    ];
}