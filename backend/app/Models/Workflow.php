<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'steps',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'steps' => 'array',
        'is_active' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function procurementRequests()
    {
        return $this->hasMany(ProcurementRequest::class);
    }
}
