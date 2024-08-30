<?php
use Illuminate\Support\Str;

return [
    /**
     * OPCIONES DE CONFIGURACIÓN DEL COMPONETNE, LA MAYORIA SON TOMADOS
     * DE FORMA AUTOMATICA
     */
    "options" => [
        'type' => 'html',
        'format' => 'YYYY-MM-DD',
        'locale' => 'en',
        'perPage' => 20,
        'perPageSelect' => [5, 20, 50, 100, 150],
        'labels' => [
            'placeholder' => 'Search...',
            'searchTitle' => 'Search within table',
            'perPage' => 'entries per page',
            'pageTitle' => 'Page {page}',
            'noRows' => 'No entries found',
            'noResults' => 'No results match your search query',
            'info' => 'Showing {start} to {end} of {rows} entries',
        ],
    ],
    /**
     * NOMBRE DEL ARCHIVO GENERADO
     */
    "export" => [
        "name" => "RDT_".Str::slug(env('APP_NAME', 'laravel'), '_')
    ]
];