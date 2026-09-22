<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('settings.manage'), 403);

        $branches = Branch::query()->pluck('name', 'id');
        $entries = AuditLog::query()->with('user')->latest()->limit(200)->get()->map(function (AuditLog $entry) use ($branches): array {
            $metadata = $entry->metadata ?? [];
            $branchId = $metadata['branch_id'] ?? null;

            return [
                'id' => $entry->id, 'created_at' => $entry->created_at, 'user' => $entry->user?->only(['name', 'email']),
                'action' => str($entry->event)->afterLast('.')->replace('_', ' ')->toString(),
                'module' => str($entry->event)->before('.')->replace('_', ' ')->toString(),
                'entity' => class_basename((string) $entry->subject_type).($entry->subject_id ? ' #'.$entry->subject_id : ''),
                'branch' => $branchId ? $branches->get($branchId, '—') : '—', 'metadata' => $metadata,
            ];
        });

        return Inertia::render('Audit/Index', ['entries' => $entries]);
    }
}
