<?php

/**
 * calling environment variables  
 */
return [
    'log' => array(
        'query' => env('ENABLE_QUERY_LOG', 0),
        'api' => env('ENABLE_API_LOG', 0)
    ),
    's3_url' => env('S3_URL'),
    'push_env' => env('PUSH_ENVIRONMENT'),
    'ejjaber' => array(
        'server' => env('SERVER'),
        'rpc_server' => env('RPC_SERVER'),
        'host' => env('JABBER_HOST'),
    ),
    'admin_email' => env('FROM_EMAIL'),
    'admin_email_for_user_excel' => env('EMAIL_FOR_USER_EXCEL'),
    'in_app_environment' => env('IN_APP_ENVIRONMENT')
];
