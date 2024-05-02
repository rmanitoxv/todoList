<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TodoGroup extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    protected $fillable = [
        'title',
        'deadline'
    ];

    public function todo()
    {
        return $this->hasMany(Todo::class);
    }
    public function file()
    {
        return $this->hasMany(File::class);
    }
}
