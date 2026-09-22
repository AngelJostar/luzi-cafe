<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateMobileProfileRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->profile($request->user()),
        ]);
    }

    public function update(UpdateMobileProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update($request->validated());

        return response()->json([
            'data' => $this->profile($user->fresh()),
            'message' => 'Perfil actualizado correctamente.',
        ]);
    }

    private function profile(User $user): array
    {
        $user->load('preferredBranch:id,name,code');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'loyalty_points' => $user->loyalty_points,
            'preferred_branch' => $user->preferredBranch,
            'created_at' => $user->created_at,
        ];
    }
}
