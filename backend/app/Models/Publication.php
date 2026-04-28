<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'publication_type',
        'publication_date',
        'closing_date',
        'estimated_value',
        'status',
        'created_by',
        'committee_id'
    ];

    protected $casts = [
        'publication_date' => 'date',
        'closing_date' => 'date',
        'estimated_value' => 'decimal:2'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function committee()
    {
        return $this->belongsTo(Committee::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
