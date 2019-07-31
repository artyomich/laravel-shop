<?php

use models\Redirects;

App::before(
    function ($request) {
        // Clear view cache in sandbox (only) with every request
        if (App::environment() == 'local') {
            $cachedViewsDirectory = app('path.storage') . '/views/';
            $files = glob($cachedViewsDirectory . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
        }

        //  Редиректы.
        $redirect = Redirects::where(['source' => $_SERVER['REQUEST_URI']])->remember(120)->first();
        if ($redirect) {
            return \Redirect::to($redirect->destination);
        }
    }
);


App::after(
    function ($request, $response) {
        //
    }
);

/**
 * Ошибки.
 */
App::missing(function ($exception) {
    \components\LogErrorsComponent::log($exception->getStatusCode());
    return Redirect::to('/404/');
});

App::error(function (\Exception $exception, $code) {
    \components\LogErrorsComponent::log($code, $exception->getMessage() . ' File: ' . $exception->getFile() . ' Line: ' . $exception->getLine());
});

App::fatal(function (\Exception $exception) {
    \components\LogErrorsComponent::log(500, $exception->getMessage() . ' File: ' . $exception->getFile() . ' Line: ' . $exception->getLine());
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter(
    'auth', function () {
    if (!Sentry::check()) {
        return Redirect::to('/admin/user/login'); // FIXME: Исправить для main модуля.
    }
}
);

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter(
    'guest', function () {
    if (Sentry::check()) {
        return Redirect::to('/admin/');
    }
}
);

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter(
    'csrf', function () {
    if (Session::token() != Input::get('_token')) {
        throw new Illuminate\Session\TokenMismatchException;
    }
}
);