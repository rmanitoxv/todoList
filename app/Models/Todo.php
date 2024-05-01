<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Todo extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    protected $fillable = [
        'todo_group_id',
        'name',
        'is_done'
    ];

    public function todo_group()
    {
        return $this->belongsTo(TodoGroup::class);
    }
}
    