<?php

/*
    |--------------------------------------------------------------------------
    | Event Configuration
    |--------------------------------------------------------------------------
    |
    | This file is for configuring the event handling mechanism used within
    | the loyalty program module. You can specify the event driver here,
    | such as 'in-memory', 'kafka', or any custom implementation.
    |
    */

return [
    'driver' => env('EVENT_DRIVER', 'in-memory'),
];
