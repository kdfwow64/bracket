<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Closure;

/**
 * Class to log the input and of request.
 */
class RequestLog {

    public function handle($request, Closure $next) {

        return $next($request);
    }

    /**
     * Terminate the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Response $response
     */
    public function terminate($request, $response) {

        Log::info(['URL' => $request->url(), 'AuthDetails' => array('AccessToken' => $request->header('accessToken')), 'form_request' => $request->all(), 'JsonRequest' => $request->getContent(), 'response' => [$response->getContent()], 'created_at' => date("Y-m-d H:i:s")]);
    }

}
