<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * BaseServiceProvider works as a base class for all user defined providers
 */
class BaseServiceProvider extends ServiceProvider {

    /**
     * The default response format returned by a service
     *
     * @var array
     * message key contains success/error message
     * success key contains true/false
     * errors key contains array of key-value validation errors for each input field in JSON, if validation fails
     * status_code key contains HTTP STATUS CODE based on response
     */
    protected static $response = [
        'message' => '',
        'success' => '',
        'errors' => [],
        'result' => [],
        'statusCode' => \Symfony\Component\HttpFoundation\Response::HTTP_OK
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
