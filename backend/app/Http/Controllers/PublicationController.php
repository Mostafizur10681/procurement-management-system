<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PublicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Publication::with(['creator', 'committee', 'attachments'])
            ->latest();

        if ($request->has('publication_type')) {
            $query->where('publication_type', $request->publication_type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('publication_date', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('publication_date', '<=', $request->date_to);
        }

        $publications = $query->paginate($request->get('per_page', 15));
        return response()->json($publications);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'publication_type' => 'required|in:tender,notice,announcement',
            'publication_date' => 'required|date',
            'closing_date' => 'nullable|date|after_or_equal:publication_date',
            'estimated_value' => 'nullable|numeric|min:0',
            'committee_id' => 'nullable|exists:committees,id',
            'status' => 'sometimes|in:draft,published,closed'
        ]);

        $publication = Publication::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'publication_type' => $validated['publication_type'],
            'publication_date' => $validated['publication_date'],
            'closing_date' => $validated['closing_date'],
            'estimated_value' => $validated['estimated_value'],
            'committee_id' => $validated['committee_id'],
            'status' => $validated['status'] ?? 'draft',
            'created_by' => auth()->id()
        ]);

        return response()->json($publication, 201);
    }

    public function show(Publication $publication): JsonResponse
    {
        $publication->load(['creator', 'committee', 'attachments']);
        return response()->json($publication);
    }

    public function update(Request $request, Publication $publication): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'publication_type' => 'sometimes|required|in:tender,notice,announcement',
            'publication_date' => 'sometimes|required|date',
            'closing_date' => 'nullable|date|after_or_equal:publication_date',
            'estimated_value' => 'nullable|numeric|min:0',
            'committee_id' => 'nullable|exists:committees,id',
            'status' => 'sometimes|in:draft,published,closed'
        ]);

        $publication->update($validated);

        return response()->json($publication);
    }

    public function destroy(Publication $publication): JsonResponse
    {
        $publication->delete();
        return response()->json(null, 204);
    }

    public function publish(Publication $publication): JsonResponse
    {
        $publication->update(['status' => 'published']);
        return response()->json($publication);
    }

    public function close(Publication $publication): JsonResponse
    {
        $publication->update(['status' => 'closed']);
        return response()->json($publication);
    }
}
