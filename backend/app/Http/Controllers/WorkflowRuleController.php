<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkflowRuleController extends Controller
{
    /**
     * Get all available workflow rules
     */
    public function index(): JsonResponse
    {
        $workflowConfig = config('procurement.workflowconfig');
        $workflowConfigs = config('procurement.workflowconfigs');
        
        return response()->json([
            'workflowconfig' => $workflowConfig,
            'workflowconfigs' => $workflowConfigs
        ]);
    }

    /**
     * Get workflow rule by ID
     */
    public function show($ruleId): JsonResponse
    {
        $workflowConfigs = config('procurement.workflowconfigs');
        
        if (!isset($workflowConfigs[$ruleId])) {
            return response()->json(['error' => 'Workflow rule not found'], 404);
        }
        
        return response()->json([
            'id' => $ruleId,
            'name' => $workflowConfigs[$ruleId]
        ]);
    }

    /**
     * Get committee types
     */
    public function committeeTypes(): JsonResponse
    {
        $committeeTypes = config('procurement.committee_type');
        $committeeTypesBn = config('procurement.committee_type_bn');
        
        return response()->json([
            'committee_type' => $committeeTypes,
            'committee_type_bn' => $committeeTypesBn
        ]);
    }

    /**
     * Get workflow status options
     */
    public function statuses(): JsonResponse
    {
        $statuses = config('procurement.status');
        
        return response()->json([
            'statuses' => $statuses
        ]);
    }
}
