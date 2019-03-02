<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Device;
use Illuminate\Support\Facades\Config;
use App\Models\User;

class ApiAuth {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        $response = array(
            'status' => Config::get('constants.status.fail'),
            'statusCode' => Config::get('codes.unauthorized'),
            'message' => trans('messages.fail.unauthorized'),
        );
        $user = Device::where('access_token', $request->header('accessToken'))->first();
        if (is_object($user)) {
            $ch_user = User::withTrashed()->find($user->user_id);
            if (is_object($ch_user)) {
                if ($ch_user->trashed()) {
                    $response['message'] = trans('messages.fail.account_deleted');
                    return response()->json($response, $response['statusCode']);
                }
                if ($ch_user->status) {
                    $request->request->add(['user_id' => $user->user_id]);
                    return $next($request);
                }
            }
        }
        return response()->json($response, $response['statusCode']);
    }

}
