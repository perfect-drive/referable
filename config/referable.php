<?php

return [
    /*
     * The directories to search for Referable classes.
     */
    'directories' => [
        app_path('Enums'),
        app_path('Models'),
    ],

    /*
     * The middleware array to use for the Referable routes.
     */
    'middleware' => ['api', 'auth:sanctum'],

    /*
     * The key name to use for the referable key in the json response.
     */
    'key_name' => 'value',

    /*
     * The value name to use for the referable value in the json response.
     */
    'value_name' => 'title',

    /*
     * The base url for the referable routes.
     */
    'base_url' => 'spa/referable/',
];
