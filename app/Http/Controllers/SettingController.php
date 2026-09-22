<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    private const DEFAULTS = [
        'business_name' => 'LUZI',
        'business_email' => '',
        'business_phone' => '',
        'business_address' => '',
        'currency' => 'MXN',
        'timezone' => 'America/Mexico_City',
        'tax_rate' => '0',
        'logo' => '',
        'max_prep_minutes' => '25',
        'healthy_margin_percent' => '55',
        'low_margin_percent' => '35',
        'tips_enabled' => '1',
        'scheduled_orders_enabled' => '1',
        'maintenance_mode' => '0',
        'allow_negative_stock' => '0',
    ];

    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('settings.manage'), 403);

        return Inertia::render('Settings/Index', [
            'settings' => array_replace(self::DEFAULTS, Setting::query()->pluck('value', 'key')->all()),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        foreach ($request->validated() as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'is_public' => in_array($key, ['business_name', 'business_email', 'business_phone', 'business_address', 'currency', 'logo', 'max_prep_minutes', 'tips_enabled', 'scheduled_orders_enabled'], true)]
            );
        }

        return to_route('settings.index')->with('success', 'Configuración guardada correctamente.');
    }
}
