<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttachmentType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'allowed_extensions',
        'max_file_size',
        'is_required',
        'is_active'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
