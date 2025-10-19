<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Theme Colors
    |--------------------------------------------------------------------------
    |
    | Color palette for the Estatia ERP system. These colors are used
    | throughout the application and can be customized as needed.
    |
    */

    'colors' => [
        'primary' => 'emerald-600',
        'primary-hover' => 'emerald-700',
        'bg' => 'gray-50',
        'muted' => 'gray-500',
        'sidebar-bg' => 'emerald-900',
        'sidebar-text' => 'gray-100',
        'success' => '#10B981',       // Green-500
        'danger' => '#EF4444',        // Red-500
        'warning' => '#F59E0B',       // Amber-500
        'info' => '#3B82F6',          // Blue-500
    ],

    /*
    |--------------------------------------------------------------------------
    | Sidebar Configuration
    |--------------------------------------------------------------------------
    */

    'sidebar' => [
        'width' => '256px',           // w-64
        'bg_color' => '#064E3B',      // Emerald-900
        'text_color' => '#F3F4F6',    // Gray-100
        'active_bg' => '#047857',     // Emerald-700
        'hover_bg' => '#065F46',      // Emerald-800
    ],

    /*
    |--------------------------------------------------------------------------
    | Brand Configuration
    |--------------------------------------------------------------------------
    */

    'brand' => [
        'name' => 'Estatia',
        'tagline' => 'Property Developer ERP',
        'logo' => '/images/logo.png',
        'logo_sm' => '/images/logo-sm.png',
    ],

    /*
    |--------------------------------------------------------------------------
    | Layout Configuration
    |--------------------------------------------------------------------------
    */

    'layout' => [
        'topbar_height' => '64px',    // h-16
        'footer_height' => '48px',    // h-12
        'card_radius' => '0.5rem',    // rounded-lg
        'button_radius' => '0.375rem', // rounded-md
    ],
];
