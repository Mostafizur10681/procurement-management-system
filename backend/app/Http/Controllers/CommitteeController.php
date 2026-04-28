<?php

namespace App\Http\Controllers;

use App\Models\Committee;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CommitteeController extends Controller
{
    public function index(): JsonResponse
    {
        $committees = Committee::latest()->get();
        return response()->json($committees);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'chairperson' => 'nullable|string|max:255',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
            'members.*.email' => 'required|email',
            'members.*.role' => 'required|in:member,chair,secretary',
            'is_active' => 'boolean'
        ]);

        $committee = Committee::create($validated);

        return response()->json($committee, 201);
    }

    public function show(Committee $committee): JsonResponse
    {
        $committee->load('publications');
        return response()->json($committee);
    }

    public function update(Request $request, Committee $committee): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'chairperson' => 'nullable|string|max:255',
            'members' => 'nullable|array',
            'members.*.name' => 'required|string|max:255',
            'members.*.email' => 'required|email',
            'members.*.role' => 'required|in:member,chair,secretary',
            'is_active' => 'boolean'
        ]);

        $committee->update($validated);

        return response()->json($committee);
    }

    public function destroy(Committee $committee): JsonResponse
    {
        if ($committee->publications()->count() > 0) {
            return response()->json(['message' => 'Cannot delete committee with existing publications'], 403);
        }

        $committee->delete();
        return response()->json(null, 204);
    }
}
