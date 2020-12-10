<?php
return [
    /**
     * Service status flag
     *
     * Tells the system that the package will be used
     */
    'otp_service_enabled' => env("OTP_ENABLED", true),
    /**
     * The default service
     *
     * The service provider to use with the application
     */
    'otp_default_service' => env("OTP_SERVICE", "nexmo"),
    /**
     * The services array
     *
     * You can add your own services with their respective parameters
     * Only the service name and the handler class is required, the
     * rest contains the parameters that your class will need to use
     */
    'services' => [
        'biotekno' => [
            "class" => \tpaksu\LaravelOTPLogin\Services\BioTekno::class,
            "username" => env('OTP_USERNAME', null),
            "password" => env('OTP_PASSWORD', null),
            "transmission_id" => env('OTP_TRANSMISSION_ID', null)
        ],
        'nexmo' => [
            'class' => \tpaksu\LaravelOTPLogin\Services\Nexmo::class,
            'api_key' => env("OTP_API_KEY", null),
            'api_secret' => env('OTP_API_SECRET', null),
            'from' => env('OTP_FROM', null)
        ],
        'twilio' => [
            'class' => \tpaksu\LaravelOTPLogin\Services\Twilio::class,
            'account_sid' => env("OTP_ACCOUNT_SID", null),
            'auth_token' => env("OTP_AUTH_TOKEN", null),
            'from' => env("OTP_FROM", null)
        ]
    ],
    /**
     * The model class to identify the user
     */
    'user_model' => \App\User::class,
    /**
     * user_model's primary key to use as a unique identifier
     */
    'user_primary_key' => 'id',
    /**
     * If the phone field is in another model, this will be the place to define it,
     * if it's in the same model with user_model, set this to null. Otherwise, use the
     * relationship handle:
     *
     * for example:
     * $user->information->phone => "information"
     */
    'user_phone_relation' => null,
    /**
     * The field which contains the phone numbers
     */
    'user_phone_field' => 'phone',
    /**
     * Reference number length
     */
    'otp_reference_number_length' => 6,
    /**
     * OTP value timeout, after that time, the password will expire and can't be
     * used in the system, in seconds, default 8 hours
     */
    'otp_timeout' => 8 * 60 * 60,
    /**
     * One time password length, in most cases it is used as 5 or 6 digits.
     */
    'otp_digit_length' => 6,
    /**
     * Save the hash of the OTP to the DB, instead of writing the clean one, defensive
     */
    'encode_password' => false
];
