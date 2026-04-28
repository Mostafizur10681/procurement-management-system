<?php

namespace App\Http\Controllers;

use App\Models\VendorRelationship;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VendorRelationshipController extends Controller
{
    public function index(): JsonResponse
    {
        $relationships = VendorRelationship::with(['vendor', 'relatedVendor'])
            ->latest()
            ->get();
        return response()->json($relationships);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'related_vendor_id' => 'required|exists:vendors,id|different:vendor_id',
            'relationship_type' => 'required|in:partner,subsidiary,supplier,competitor',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $relationship = VendorRelationship::create($validated);

        return response()->json($relationship, 201);
    }

    public function show(VendorRelationship $vendorRelationship): JsonResponse
    {
        $vendorRelationship->load(['vendor', 'relatedVendor']);
        return response()->json($vendorRelationship);
    }

    public function update(Request $request, VendorRelationship $vendorRelationship): JsonResponse
    {
        $validated = $request->validate([
            'relationship_type' => 'sometimes|required|in:partner,subsidiary,supplier,competitor',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        $vendorRelationship->update($validated);

        return response()->json($vendorRelationship);
    }

    public function destroy(VendorRelationship $vendorRelationship): JsonResponse
    {
        $vendorRelationship->delete();
        return response()->json(null, 204);
    }

    public function getByVendor($vendorId): JsonResponse
    {
        $relationships = VendorRelationship::with(['relatedVendor'])
            ->where('vendor_id', $vendorId)
            ->latest()
            ->get();
        return response()->json($relationships);
    }
}
