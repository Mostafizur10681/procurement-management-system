<?php

namespace App\Http\Controllers;

use App\Models\AttachmentType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AttachmentTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $attachmentTypes = AttachmentType::where('is_active', true)
            ->orderBy('name')
            ->get();
        return response()->json($attachmentTypes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:attachment_types,code',
            'description' => 'nullable|string|max:1000',
            'allowed_extensions' => 'nullable|string|max:255',
            'max_file_size' => 'nullable|integer|min:1',
            'is_required' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $attachmentType = AttachmentType::create($validated);

        return response()->json($attachmentType, 201);
    }

    public function show(AttachmentType $attachmentType): JsonResponse
    {
        $attachmentType->load('attachments');
        return response()->json($attachmentType);
    }

    public function update(Request $request, AttachmentType $attachmentType): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'code' => 'sometimes|required|string|max:50|unique:attachment_types,code,'.$attachmentType->id,
            'description' => 'nullable|string|max:1000',
            'allowed_extensions' => 'nullable|string|max:255',
            'max_file_size' => 'nullable|integer|min:1',
            'is_required' => 'boolean',
            'is_active' => 'boolean'
        ]);

        $attachmentType->update($validated);

        return response()->json($attachmentType);
    }

    public function destroy(AttachmentType $attachmentType): JsonResponse
    {
        if ($attachmentType->attachments()->count() > 0) {
            return response()->json(['message' => 'Cannot delete attachment type with existing attachments'], 403);
        }

        $attachmentType->delete();
        return response()->json(null, 204);
    }
}
