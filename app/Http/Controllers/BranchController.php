<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBranchRequest;
use App\Http\Requests\StoreBranchServiceRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BranchController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('branches.view'), 403);

        return Inertia::render('Branches/Index', [
            'branches' => Branch::query()->with('services')->withCount(['products', 'users'])->orderBy('name')->get(),
        ]);
    }

    public function store(StoreBranchRequest $request): RedirectResponse
    {
        Branch::create($request->validated());

        return to_route('branches.index')->with('success', 'Sucursal creada correctamente.');
    }

    public function update(UpdateBranchRequest $request, Branch $branch): RedirectResponse
    {
        $branch->update($request->validated());

        return to_route('branches.index')->with('success', 'Sucursal actualizada correctamente.');
    }

    public function deactivate(Request $request, Branch $branch): RedirectResponse
    {
        abort_unless($request->user()?->can('branches.manage'), 403);
        $branch->update(['is_active' => false]);

        return to_route('branches.index')->with('success', 'Sucursal desactivada.');
    }

    public function storeService(StoreBranchServiceRequest $request, Branch $branch): RedirectResponse
    {
        $branch->services()->create($request->validated());

        return to_route('branches.index')->with('success', 'Servicio de sucursal agregado.');
    }
}
