<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', 'Web\PageController@index')->name('home');

Auth::routes(['verify' => true]);
Route::group(['middleware' => ['auth', 'verified']], function () {
    Route::get('/home', 'User\DashboardController@index')->name('home');
});

Route::group(['prefix' => 'admin'], function () {
    // Authentication Routes...
    Route::get('/', 'Panel\Auth\LoginController@showLoginForm')->name('panel.login');
    Route::post('/', 'Panel\Auth\LoginController@login');
    Route::post('logout', 'Panel\Auth\LoginController@logout')->name('panel.logout');

    Route::get('/register', 'Panel\Auth\RegisterController@showRegistrationForm')->name('panel.register');
    Route::post('/register', 'Panel\Auth\RegisterController@register');

    // Password Reset Routes...
    Route::post('password/email', 'Panel\Auth\ForgotPasswordController@sendResetLinkEmail')->name('panel.password.email');
    Route::get('password/reset', 'Panel\Auth\ForgotPasswordController@showLinkRequestForm')->name('panel.password.request');
    Route::post('password/reset', 'Panel\Auth\ResetPasswordController@reset')->name('panel.password.update');
    Route::get('password/reset/{token}', 'Panel\Auth\ResetPasswordController@showResetForm')->name('panel.password.reset');

    Route::group(['middleware' => 'auth:panelAdmin'], function () {
        Route::get('dashboard', 'Panel\DashboardController@index')->name('panel.dashboard');

        #Users...
        Route::resource('users', 'Panel\UserController', ["as"=>"admin"]);

        #Orders...
        Route::resource('orders', 'Panel\OrderController', ["as"=>"admin"]);

        #Drip-feed...
        Route::resource('drip-feed', 'Panel\DripFeedController', ["as"=>"admin"]);

        #Tasks...
        Route::resource('tasks', 'Panel\TaskController', ["as"=>"admin"]);

        #Services...
        Route::resource('services', 'Panel\ServiceController', ["as"=>"admin"]);

        #Payments...
        Route::resource('payments', 'Panel\PaymentController', ["as"=>"admin"]);

        #Tickets...
        Route::resource('tickets', 'Panel\TicketController', ["as"=>"admin"]);

        #Tickets...
        Route::resource('reports', 'Panel\ReportController', ["as"=>"admin"]);

        #Rppearance...
        Route::resource('appearance', 'Panel\AppearanceController', ["as"=>"admin"]);

        #Blog...
        Route::resource('blog', 'Panel\BlogController', ["as"=>"admin"]);


        #Profile...
        Route::get('profile', 'Panel\ProfileController@profile')->name('admin.profile');
        Route::put('password/update', 'Panel\ProfileController@passwordUpdate')->name('admin.password.update');

        #Settings...
        Route::group(['prefix' => 'setting'], function () {
            Route::get('general', 'Panel\Setting\GeneralController@index')->name('admin.setting.general');
            Route::post('general-update', 'Panel\Setting\GeneralController@generalUpdate')->name('admin.setting.generalUpdate');
            Route::resource('faq', 'Panel\Setting\FaqController', ["as"=>"admin.setting"]);
            Route::resource('provider', 'Panel\Setting\ProviderController', ["as"=>"admin.setting"]);
            Route::put('provider-update', 'Panel\Setting\ProviderController@update')->name('provider.update');
            Route::delete('provider-delete', 'Panel\Setting\ProviderController@destroy')->name('provider.delete');
            Route::resource('payment', 'Panel\Setting\PaymentController', ["as"=>"admin.setting"]);
            Route::get('module', 'Panel\Setting\ModuleController@index')->name('admin.setting.module');
            Route::get('notification', 'Panel\Setting\NotificationController@index')->name('admin.setting.notification');
            Route::resource('bonuses', 'Panel\Setting\BonusesController', ["as"=>"admin.setting"]);

        });

    });
});
