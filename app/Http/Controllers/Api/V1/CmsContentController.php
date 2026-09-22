<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CmsContent;
use Illuminate\Http\JsonResponse;

class CmsContentController extends Controller
{
    public function index(): JsonResponse
    {
        $contents = CmsContent::query()
            ->where('status', 'published')
            ->orWhere(fn ($query) => $query->where('status', 'scheduled')->where('scheduled_at', '<=', now()))
            ->orderBy('area')
            ->get()
            ->map(fn (CmsContent $content) => [
                'key' => $content->key,
                'area' => $content->area,
                'type' => $content->type,
                'label' => $content->label,
                'value' => $content->value,
                'alt_text' => $content->alt_text,
                'image_url' => $content->image_path ? asset('storage/'.$content->image_path) : null,
            ]);

        return response()->json(['data' => $contents]);
    }
}
