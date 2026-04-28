<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'attachment_type_id',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'description',
        'uploaded_by'
    ];

    public function attachable()
    {
        return $this->morphTo();
    }

    public function attachmentType()
    {
        return $this->belongsTo(AttachmentType::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
