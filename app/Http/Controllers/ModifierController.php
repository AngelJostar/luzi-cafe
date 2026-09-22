<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreModifierGroupRequest;
use App\Http\Requests\StoreModifierOptionRequest;
use App\Models\ModifierGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ModifierController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('products.manage'), 403);

        return Inertia::render('Modifiers/Index', [
            'groups' => ModifierGroup::query()->with('options')->orderBy('display_order')->get(),
        ]);
    }

    public function storeGroup(StoreModifierGroupRequest $request): RedirectResponse
    {
        ModifierGroup::query()->create($request->validated() + ['display_order' => ModifierGroup::query()->max('display_order') + 1]);

        return back();
    }

    public function storeOption(StoreModifierOptionRequest $request, ModifierGroup $modifierGroup): RedirectResponse
    {
        $modifierGroup->options()->create($request->validated() + ['display_order' => $modifierGroup->options()->max('display_order') + 1]);

        return back();
    }
}
