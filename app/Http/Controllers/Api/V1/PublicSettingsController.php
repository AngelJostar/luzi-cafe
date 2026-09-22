<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class PublicSettingsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Setting::query()->where('is_public', true)->pluck('value', 'key'),
        ]);
    }
}
