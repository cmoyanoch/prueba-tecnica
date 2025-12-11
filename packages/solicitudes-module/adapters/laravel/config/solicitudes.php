<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración del Módulo Solicitudes
    |--------------------------------------------------------------------------
    */

    // Canal de log para auditoría
    'audit_log_channel' => env('SOLICITUDES_AUDIT_LOG_CHANNEL', 'solicitudes'),

    // Habilitar eventos de dominio
    'enable_domain_events' => env('SOLICITUDES_ENABLE_EVENTS', true),

    // Paginación por defecto
    'pagination' => [
        'per_page' => env('SOLICITUDES_PER_PAGE', 15),
        'max_per_page' => env('SOLICITUDES_MAX_PER_PAGE', 100),
    ],

    // Prefijo de rutas API
    'api_prefix' => env('SOLICITUDES_API_PREFIX', 'api/solicitudes'),

    // Middleware para las rutas
    'middleware' => ['api'],
];
