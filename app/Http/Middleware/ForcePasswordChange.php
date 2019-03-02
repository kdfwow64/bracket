<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class ForcePasswordChange {

    /**
     * Force redirect user to change password page if the user has clicked in forget password and has logged in from temporary password.
     * Stop redirection if user has changed password or if user wants to logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (Auth::user() && Auth::user()->is_reset_password == 1 && ($request->path() != 'admin/change-password') && ($request->path() != 'admin/logout')) {
            Session::flash('status_fail', trans('messages.admin.force_change_password'));
            return Redirect::to('admin/change-password');
        }

        return $next($request);
    }

}
