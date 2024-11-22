<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     */
    protected $middleware = [
        // Otros middlewares globales aquí
        \App\Http\Middleware\CorsMiddleware::class,


    ];

    /**
     * The application's route middleware groups.
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\CorsMiddleware::class,
            // Middlewares para rutas web

            
        ],

        'api' => [
            // Middlewares para rutas API
        ],
    ];

    /**
     * The application's route middleware.
     */
    protected $routeMiddleware = [
        // Otros middlewares específicos de rutas aquí
    ];
}
