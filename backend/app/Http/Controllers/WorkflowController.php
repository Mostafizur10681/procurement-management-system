<?php

namespace App\Http\Controllers;

use App\Models\Workflow;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkflowController extends Controller
{
    public function index(): JsonResponse
    {
        $workflows = Workflow::with('creator')->latest()->get();
        return response()->json($workflows);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'steps' => 'required|array|min:1',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.description' => 'nullable|string',
            'steps.*.assigned_role' => 'required|in:admin,manager,user',
            'steps.*.order' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $workflow = Workflow::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'steps' => $validated['steps'],
            'is_active' => $validated['is_active'] ?? true,
            'created_by' => auth()->id()
        ]);

        return response()->json($workflow, 201);
    }

    public function show(Workflow $workflow): JsonResponse
    {
        $workflow->load('creator', 'procurementRequests');
        return response()->json($workflow);
    }

    public function update(Request $request, Workflow $workflow): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'steps' => 'sometimes|required|array|min:1',
            'steps.*.name' => 'required|string|max:255',
            'steps.*.description' => 'nullable|string',
            'steps.*.assigned_role' => 'required|in:admin,manager,user',
            'steps.*.order' => 'required|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $workflow->update($validated);

        return response()->json($workflow);
    }

    public function destroy(Workflow $workflow): JsonResponse
    {
        if ($workflow->procurementRequests()->count() > 0) {
            return response()->json(['message' => 'Cannot delete workflow with existing requests'], 403);
        }

        $workflow->delete();
        return response()->json(null, 204);
    }
}
