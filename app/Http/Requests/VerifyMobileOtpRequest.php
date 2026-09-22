<?php

namespace App\Http\Requests;

class VerifyMobileOtpRequest extends MobileEmailRequest
{
    public function rules(): array
    {
        return [...parent::rules(), 'code' => ['required', 'digits:6']];
    }
}
