<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */

    protected $except = [
        'http://127.0.0.1:8000/add-website',
        'https://phplaravel-1142719-4507399.cloudwaysapps.com/add-website',

        'http://127.0.0.1:8000/add-payment',
        'https://phplaravel-1142719-4507399.cloudwaysapps.com/add-payment',
    ];
}