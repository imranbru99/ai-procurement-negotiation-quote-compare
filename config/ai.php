<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo mode
    |--------------------------------------------------------------------------
    |
    | When true, AIHub is faked with deterministic procurement responses and
    | no live API keys are required.
    |
    */
    'demo_mode' => (bool) env('AI_DEMO_MODE', true),

    'default_provider' => env('AI_DEFAULT_PROVIDER', 'openai'),

    'default_model' => env('AI_DEFAULT_MODEL', 'gpt-4o-mini'),

];
