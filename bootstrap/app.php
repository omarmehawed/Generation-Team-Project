<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\IsAdmin;   // استيراد كلاس الأدمن
use App\Http\Middleware\CheckRole; // استيراد كلاس الرول

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // دمجنا الاثنين هنا في مصفوفة واحدة
        $middleware->alias([
            'role'    => CheckRole::class, // عشان تستخدم middleware('role:student')
            'isAdmin' => IsAdmin::class,   // عشان تستخدم middleware('isAdmin')
        ]);

        // 👇 ضيف السطر ده عشان يحل مشكلة الـ HTTPS على Railway
        $middleware->trustProxies(at: '*');

        // 👇 ضيف السطر ده عشان السيستم يفهم كلمة permission
        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckPermission::class, // دلوقتي هيلاقيه صح
            'role' => \App\Http\Middleware\CheckRole::class,             // وده كمان
            'isAdmin' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
