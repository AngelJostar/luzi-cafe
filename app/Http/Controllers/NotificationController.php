<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateNotificationTemplateRequest;
use App\Models\NotificationTemplate;
use App\Models\OperationalNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('orders.view'), 403);

        return Inertia::render('Notifications/Index', [
            'notifications' => OperationalNotification::query()->with('branch')->latest()->limit(100)->get(),
            'templates' => NotificationTemplate::query()->orderBy('code')->get(),
        ]);
    }

    public function markRead(Request $request, OperationalNotification $notification): RedirectResponse
    {
        abort_unless($request->user()?->can('orders.view'), 403);
        $notification->update(['read_at' => now()]);

        return to_route('notifications.index');
    }

    public function updateTemplate(UpdateNotificationTemplateRequest $request, NotificationTemplate $template): RedirectResponse
    {
        $template->update($request->validated());

        return to_route('notifications.index')->with('success', 'Plantilla actualizada.');
    }
}
