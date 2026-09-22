<?php

return [
    // En local permite avanzar sin correo; producción debe exigirlo siempre.
    'require_otp' => (($override = getenv('MOBILE_REQUIRE_OTP')) !== false)
        ? filter_var($override, FILTER_VALIDATE_BOOL)
        : getenv('APP_ENV') === 'production',
];
