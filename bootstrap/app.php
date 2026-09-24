<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Trust all proxies (Cloudflare + Nginx reverse proxy)
        // Required for signed URL validation (Livewire file uploads),
        // correct HTTPS detection, and accurate client IP resolution
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'portal/auth/direct-login',
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'lab_staff' => \App\Http\Middleware\EnsureLabStaff::class,
            'partner_access' => \App\Http\Middleware\EnsurePartnerPortal::class,
            'patient_portal_access' => \App\Http\Middleware\EnsurePatientSession::class,
            'lab_api_key' => \App\Http\Middleware\ValidateLabApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
