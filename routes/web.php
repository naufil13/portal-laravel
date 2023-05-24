<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/validateToken', 'SssoController@index')->name('validateToken');
Route::get('/tokenValidate/{username}/{applicationCode}/{SSOtoken}', 'SssoController@validateToken')->name('tokenValidate');
Route::get('/validateApplicationUser', 'SssoController@userAuthForMobileApp')->name('validateApplicationUser');
Route::post('/limsValidateToken', 'SssoController@index')->name('limsValidateToken');
Route::get('/guestSupport', 'GuestSupportController@index');
Route::post('/guestSupport', 'GuestSupportController@store');


Route::get("artisan",function(){
    \Artisan::call('config:cache');
});


Route::get('/', 'Admin\LoginController@index');
Route::get('/dummy', 'GuestSupportController@dummy');

Route::get(config('app.admin_dir') . '/login', 'Admin\LoginController@index');
Route::post(config('app.admin_dir') . '/login/do_login', 'Admin\LoginController@do_login');
Route::post(config('app.admin_dir') . '/login/forgetPasswordSubmissions', 'Admin\LoginController@forgetPasswordSubmissions');
Route::get(config('app.admin_dir') . '/login/forgotPassword', 'Admin\LoginController@forgotPassword');
Route::get(config('app.admin_dir') . '/login/resetLink/{token}', 'Admin\LoginController@resetPasswordLink');
Route::post(config('app.admin_dir') . '/login/resetPassword', 'Admin\LoginController@resetPassword');
Route::get(config('app.admin_dir') . '/login/logout', 'Admin\LoginController@logout');
// Route::get(config('app.admin_dir') . '/getDivisionByCompany/{id}', 'Admin\DivisionController@getDivisionByCompany');
Route::middleware('auth')->prefix(config('app.admin_dir'))->group(function () {
    Route::get('/user_info/change_pass_first_attempt', function () {
        return app('App\Http\Controllers\Admin\UserInfoController')->change_pass_first_attempt();
    });
    Route::post('/user_info/pass_change_first_attempt', function () {
        return app('App\Http\Controllers\Admin\UserInfoController')->pass_change_first_attempt();
    });

    Route::get('/user_info/pin_confirmation', function () {
        return app('App\Http\Controllers\Admin\UserInfoController')->pin_confirmation();
    });
    Route::post('/user_info/pin_confirmation_post', function () {
        return app('App\Http\Controllers\Admin\UserInfoController')->pin_confirmation_post();
    });
});

Route::middleware(['auth', 'admin', 'is_default_password_changed', 'is_login_pin_verified', 'isActiveUser'])->prefix(config('app.admin_dir'))->group(function () {

    Route::any(
        '/{controller}/{method?}/{params?}',
        function ($controller, $method = 'index', $params = null) {
            $app = app();
            $controller = Str::studly(Str::singular($controller));
            // dynamic making controller from url
            $controller_cls = "App\Http\Controllers\\" . Str::studly(config('app.admin_dir')) . "\\{$controller}Controller";
            if (class_exists($controller_cls)) {
                $controller = $app->make($controller_cls);
                try {
                    return $controller->callAction($method, ['params' => $params]);
                } catch (Exception $e) {
                    developer_log('', $e);
                    return $e;
                    return View::make('errors.error');
                }
            } else {
                return View::make('errors.404');
            }
        }
    )->where('params', '[A-Za-z0-9-_/]+');
});


Auth::routes();

Route::any(
    '/{controller}/{method?}/{params?}',
    function ($controller, $method = 'index', $params = null) {
        $app = app();
        $controller = Str::studly(Str::singular($controller));
        /*if(in_array($controller, ['Cron'])){
            return View::make('errors.404');
        }*/
        $controller_cls = "App\Http\Controllers\\{$controller}Controller";
        if (class_exists($controller_cls)) {
            $controller = $app->make($controller_cls);
            try {
                return $controller->callAction($method, ['params' => $params]);
            } catch (Exception $e) {
                return View::make('errors.404');
            }
        } else {
            return View::make('errors.404');
        }
    }
)->where('params', '[A-Za-z0-9-_/]+');
