<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'chairperson',
        'members',
        'is_active'
    ];

    protected $casts = [
        'members' => 'array',
        'is_active' => 'boolean'
    ];

    public function publications()
    {
        return $this->hasMany(Publication::class);
    }
}
