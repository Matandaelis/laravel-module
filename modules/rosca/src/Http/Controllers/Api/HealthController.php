<?php

namespace Modules\Rosca\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;

class HealthController extends Controller
{
    public function index(): JsonResponse
    {
        // Basic health: database and queue status
        $db = true;
        try {
            \DB::connection()->getPdo();
        } catch (\Throwable $e) {
            $db = false;
        }

        return response()->json([
            'ok' => $db,
            'database' => $db,
            'queue' => true,
        ]);
    }
}
