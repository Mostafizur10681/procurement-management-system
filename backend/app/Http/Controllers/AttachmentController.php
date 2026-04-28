<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\AttachmentType;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Attachment::with(['attachmentType', 'uploader'])
            ->latest();

        if ($request->has('attachment_type_id')) {
            $query->where('attachment_type_id', $request->attachment_type_id);
        }

        if ($request->has('attachable_type')) {
            $query->where('attachable_type', $request->attachable_type);
        }

        $attachments = $query->paginate($request->get('per_page', 15));
        return response()->json($attachments);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'attachable_type' => 'required|string',
            'attachable_id' => 'required|integer',
            'attachment_type_id' => 'required|exists:attachment_types,id',
            'file' => 'required|file|max:' . ($request->attachment_type_id ? $this->getMaxFileSize($request->attachment_type_id) : 10240),
            'description' => 'nullable|string|max:1000'
        ]);

        $attachmentType = AttachmentType::find($validated['attachment_type_id']);
        
        if ($attachmentType && $attachmentType->allowed_extensions) {
            $allowedExtensions = explode(',', $attachmentType->allowed_extensions);
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                return response()->json(['error' => 'File type not allowed'], 422);
            }
        }

        $filePath = $request->file('file')->store('attachments', 'public');

        $attachment = Attachment::create([
            'attachable_type' => $validated['attachable_type'],
            'attachable_id' => $validated['attachable_id'],
            'attachment_type_id' => $validated['attachment_type_id'],
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $request->file('file')->getSize(),
            'mime_type' => $request->file('file')->getMimeType(),
            'description' => $validated['description'],
            'uploaded_by' => auth()->id()
        ]);

        return response()->json($attachment, 201);
    }

    public function show(Attachment $attachment): JsonResponse
    {
        $attachment->load(['attachmentType', 'uploader', 'attachable']);
        return response()->json($attachment);
    }

    public function download(Attachment $attachment)
    {
        if (!Storage::disk('public')->exists($attachment->file_path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    public function destroy(Attachment $attachment): JsonResponse
    {
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();
        return response()->json(null, 204);
    }

    private function getMaxFileSize($attachmentTypeId): int
    {
        $attachmentType = AttachmentType::find($attachmentTypeId);
        return $attachmentType && $attachmentType->max_file_size 
            ? $attachmentType->max_file_size * 1024 
            : 10240; // Default 10MB
    }
}
