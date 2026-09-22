<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteMobileRegistrationRequest;
use App\Http\Requests\MobileEmailRequest;
use App\Http\Requests\VerifyMobileOtpRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class MobileOnboardingController extends Controller
{
    public function checkEmail(MobileEmailRequest $request): JsonResponse
    {
        return response()->json([
            'exists' => User::query()->where('email', $request->string('email')->lower()->toString())->exists(),
        ]);
    }

    public function sendOtp(MobileEmailRequest $request): JsonResponse
    {
        $email = $request->string('email')->lower()->toString();
        if (User::query()->where('email', $email)->exists()) {
            throw ValidationException::withMessages(['email' => 'Este correo ya tiene una cuenta. Inicia sesión.']);
        }

        $code = (string) random_int(100000, 999999);
        DB::table('mobile_email_verifications')->where('email', $email)->delete();
        DB::table('mobile_email_verifications')->insert([
            'email' => $email, 'code' => Hash::make($code), 'expires_at' => now()->addMinutes(10), 'created_at' => now(), 'updated_at' => now(),
        ]);
        Mail::raw("Tu código de verificación Luzi es: {$code}. Vence en 10 minutos.", function ($message) use ($email): void {
            $message->to($email)->subject('Tu código de verificación Luzi');
        });

        return response()->json(['message' => 'Código enviado.', 'expires_in' => 600]);
    }

    public function verifyOtp(VerifyMobileOtpRequest $request): JsonResponse
    {
        $email = $request->string('email')->lower()->toString();
        $verification = DB::table('mobile_email_verifications')->where('email', $email)->latest('id')->first();
        if (! $verification || now()->greaterThan($verification->expires_at) || $verification->attempts >= 5 || ! Hash::check($request->string('code')->toString(), $verification->code)) {
            if ($verification) {
                DB::table('mobile_email_verifications')->where('id', $verification->id)->increment('attempts');
            }
            throw ValidationException::withMessages(['code' => 'El código no es válido o ha vencido.']);
        }
        DB::table('mobile_email_verifications')->where('id', $verification->id)->update(['verified_at' => now(), 'updated_at' => now()]);

        return response()->json(['message' => 'Correo verificado.']);
    }

    public function complete(CompleteMobileRegistrationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $verification = DB::table('mobile_email_verifications')->where('email', strtolower($data['email']))->whereNotNull('verified_at')->where('expires_at', '>', now())->latest('id')->first();
        if (config('mobile.require_otp') && ! $verification) {
            throw ValidationException::withMessages(['email' => 'Primero valida el código enviado a tu correo.']);
        }

        $user = User::create(['name' => $data['name'], 'email' => strtolower($data['email']), 'password' => $data['password'], 'email_verified_at' => now()]);
        $user->assignRole('cliente');
        if ($verification) {
            DB::table('mobile_email_verifications')->where('id', $verification->id)->delete();
        }

        return response()->json(['token' => $user->createToken($data['device_name'], ['mobile'])->plainTextToken, 'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email]], 201);
    }
}
