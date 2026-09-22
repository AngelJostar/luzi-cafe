<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginMobileRequest;
use App\Http\Requests\RegisterMobileCustomerRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    public function register(RegisterMobileCustomerRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        $user->assignRole('cliente');

        return $this->authenticatedResponse($user, 'mobile-registration', 201);
    }

    public function login(LoginMobileRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->string('email'))->first();
        if (! $user || ! Hash::check($request->string('password'), $user->password)) {
            throw ValidationException::withMessages(['email' => 'Las credenciales no son válidas.']);
        }

        return $this->authenticatedResponse($user, $request->string('device_name'));
    }

    public function logout(): JsonResponse
    {
        request()->user()->currentAccessToken()?->delete();

        return response()->json(status: 204);
    }

    private function authenticatedResponse(User $user, string $deviceName, int $status = 200): JsonResponse
    {
        return response()->json([
            'token' => $user->createToken($deviceName, ['mobile'])->plainTextToken,
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ], $status);
    }
}
