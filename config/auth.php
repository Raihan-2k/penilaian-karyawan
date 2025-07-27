<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a good starting point.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses the session storage driver and Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's social
    | state.
    |
    | Laravel also includes a "remember me" authentication feature that
    | will keep users logged in for an extended period of time. If
    | you choose to utilize this feature, you'll simply specify a
    | duration in seconds.
    |
    */

    'guards' => [
        'web' => [ // <--- Ini adalah guard utama kita sekarang
            'driver' => 'session',
            'provider' => 'users', // Provider ini akan menunjuk ke model Employee
        ],
        // PASTIKAN BLOK 'web_employee_login' TIDAK ADA DI SINI
        // Ini adalah contoh jika ada:
        // 'web_employee_login' => [
        //     'driver' => 'session',
        //     'provider' => 'employee_logins',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's social
    | state.
    |
    | If you have several different user tables or models, you may configure
    | several sources that represent these models. These providers may then
    | be assigned to any authentication guard using the provider option.
    |
    | Laravel also includes a "remember me" authentication feature that
    | will keep users logged in for an extended period of time. If
    | you choose to utilize this feature, you'll simply specify a
    | duration in seconds.
    |
    */

    'providers' => [
        'users' => [ // <--- Ini adalah provider utama kita sekarang
            'driver' => 'eloquent',
            'model' => App\Models\User::class, 
        ],
        // PASTIKAN BLOK 'employee_logins' TIDAK ADA DI SINI
        // Ini adalah contoh jika ada:
        // 'employee_logins' => [
        //     'driver' => 'eloquent',
        //     // 'model' => App\Models\EmployeeLogin::class, // Model ini sudah dihapus
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify how many seconds before a reset token expires. This
    | security feature keeps each token from being abused after it's
    | issued.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60 * 60, // 1 hour
            'throttle' => 60 * 10, // 10 minutes
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => 10800,

];