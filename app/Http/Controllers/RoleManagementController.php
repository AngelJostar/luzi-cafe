<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStaffUserRequest;
use App\Http\Requests\UpdateUserRoleRequest;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleManagementController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->can('users.manage'), 403);

        return Inertia::render('Roles/Index', [
            'roles' => Role::query()->with('permissions')->orderBy('name')->get(),
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'users' => User::query()->with(['roles', 'branches:id,name'])->whereDoesntHave('roles', fn ($query) => $query->where('name', 'cliente'))->orderBy('name')->get(['id', 'name', 'email', 'is_blocked', 'force_password_change', 'created_at']),
        ]);
    }

    public function updateUserRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id && $request->input('role') !== 'superadmin') {
            return back()->withErrors(['role' => 'No puedes retirar tu propio rol de superadministrador.']);
        }

        $user->syncRoles([$request->string('role')]);

        return to_route('roles.index')->with('success', 'Rol actualizado correctamente.');
    }

    public function store(StoreStaffUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $user = User::create([
            'name' => $data['name'], 'email' => $data['email'], 'password' => $data['password'],
            'is_blocked' => $data['is_blocked'], 'force_password_change' => $data['force_password_change'],
        ]);
        $user->syncRoles([$data['role']]);
        $user->branches()->sync($data['branch_ids'] ?? []);

        return to_route('roles.index')->with('success', 'Usuario creado correctamente.');
    }
}
