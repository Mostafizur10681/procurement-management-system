<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorRelationship extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'related_vendor_id',
        'relationship_type',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function relatedVendor()
    {
        return $this->belongsTo(Vendor::class, 'related_vendor_id');
    }
}
