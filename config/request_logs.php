<?php

return [
    'enabled' => env('REQUEST_LOG_ENABLED', true),

    // Campos mascarados no payload
    'masked_fields' => [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'secret',
        'two_factor_secret',
        'recovery_codes',
        'card_number',
        'cvv',
    ],

    // Rotas excluídas do log (suporta wildcards)
    'excluded_routes' => [
        'api/health',
        'api/auth/email/verify/*',
    ],

    // Logar o corpo da response (cuidado com responses grandes)
    'log_response_body' => env('REQUEST_LOG_RESPONSE_BODY', false),

    // Limite de caracteres do payload/response
    'max_body_length' => 2000,
];
