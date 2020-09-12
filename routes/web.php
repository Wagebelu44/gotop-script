<?php

use Illuminate\Support\Facades\Route;

//Test Route::START
Route::get('command', function () {
    \Artisan::call('cache:forget spatie.permission.cache');
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('route:clear');
    dd("All clear!");
});
//Test Route::END


Route::group(['middleware' => 'checkPanel'], function () {
    Route::get('/', 'Web\PageController@index')->name('home');

    Auth::routes(['verify' => true]);
    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::get('/home', 'User\DashboardController@index')->name('home');

        /* User order module */
        Route::get('/order/{order_id?}', 'User\OrderController@index')->name('order');
        Route::get('/get-category-services', 'User\OrderController@getCateServices');
        Route::get('/orders', 'User\OrderController@orderLists');
        Route::post('/make_new_order', 'User\OrderController@store');
        Route::post('/mass-order-store', 'User\OrderController@storeMassOrder')->name('massOrder.store');

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

        Route::group(['middleware' => 'auth:panelAdmin', 'as' => 'admin.'], function () {
            Route::get('dashboard', 'Panel\DashboardController@index')->name('panel.dashboard');

            #Users...
            Route::post('updatePassword', 'Panel\UserController@updatePassword');
            Route::post('suspendUser', 'Panel\UserController@suspend');
            Route::get('getusers', 'Panel\UserController@getUsers');
            Route::resource('users', 'Panel\UserController');
            #Users...
            Route::post('updatePassword', 'Panel\UserController@updatePassword');
            Route::post('suspendUser', 'Panel\UserController@suspend');
            Route::get('getusers', 'Panel\UserController@getUsers');
            Route::get('users-services/{user_id}', 'Panel\UserController@getUserServices');
            Route::get('category-services', 'Panel\UserController@getCategoryService');
            Route::post('store-service', 'Panel\UserController@serviceUpdate');
            Route::DELETE('delete-user-service', 'Panel\UserController@deleteUsersService');
            Route::post('bulk-status-update', 'Panel\UserController@bulkUserUpdate');
            Route::resource('users', 'Panel\UserController');

            #Orders...
            Route::get('get-orders', 'Panel\OrderController@getOrderLists');
            Route::resource('orders', 'Panel\OrderController');

            #Drip-feed...
            Route::resource('drip-feed', 'Panel\DripFeedController');

            #Services...
            Route::get('service_provider', 'Panel\ServiceController@getProviders');
            Route::post('provider/get/services', 'Panel\ServiceController@getProviderServices');
            Route::post('service_bulk_category', 'Panel\ServiceController@bulkCategory');
            Route::post('service_bulk_enable', 'Panel\ServiceController@bulkEnable');
            Route::post('service_bulk_delete', 'Panel\ServiceController@bulkDelete');
            Route::post('service_bulk_disable', 'Panel\ServiceController@bulkDisable');
            Route::post('category-status-change/{id}', 'Panel\ServiceController@enablingCategory');
            Route::get('enableService/{id}', 'Panel\ServiceController@enableService');
            Route::DELETE('deleteService/{id}', 'Panel\ServiceController@deleteService');
            Route::get('duplicate/service/{service_id}', 'Panel\ServiceController@duplicateService');
            Route::get('show-category/{id}', 'Panel\ServiceController@showCategory');
            Route::post('category-store', 'Panel\ServiceController@categoryStore');
            Route::get('get-category-services', 'Panel\ServiceController@getCateServices');
            Route::post('updateService/{id}', 'Panel\ServiceController@updateService');
            Route::resource('services', 'Panel\ServiceController', ["as"=>"admin"]);
            #Tasks...
            Route::resource('tasks', 'Panel\TaskController');

            #Services...
            Route::resource('services', 'Panel\ServiceController');

            #Payments...
            Route::resource('payments', 'Panel\PaymentController');

            #Tickets...
            Route::resource('tickets', 'Panel\TicketController');
            Route::get('tickets/status/{status}/{ticket_id}', 'Panel\TicketController@changeTicketStatus')->name('tickets.status.change');
            Route::post('tickets/status/changes', 'Panel\TicketController@changeBulkStatus')->name('tickets.status.changes');
            Route::post('tickets/{ticket}/comment', 'Panel\TicketController@comment')->name('tickets.comment');

            #Reports...
            Route::resource('reports', 'Panel\ReportController');

            #Rppearance...
            Route::resource('appearance', 'Panel\AppearanceController');
            Route::post('appearance-status', 'Panel\AppearanceController@updateStatus')->name('appearance.updateStatus');

            #Rppearance menu...
            Route::resource('menu', 'Panel\MenuController');
            Route::post('menu-sortable', 'Panel\MenuController@sortableMenu')->name('menu.sortable');
            Route::resource('theme', 'Panel\ThemeController')->only('index', 'edit', 'update');
            Route::post('theme-active/{id}', 'Panel\ThemeController@active')->name('theme.active');
            Route::post('theme-page-reset/{id}', 'Panel\ThemeController@reset')->name('theme.reset');

            #blog...
            Route::resource('blog', 'Panel\BlogController');

            #blog Category
            Route::resource('blog-category', 'Panel\BlogCategoryController');

            #blog Slider
            Route::resource('blog-slider', 'Panel\BlogSliderController');


            #Profile...
            Route::get('profile', 'Panel\ProfileController@profile')->name('profile');
            Route::put('password/update', 'Panel\ProfileController@passwordUpdate')->name('password.update');

            #Settings...
            Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
                Route::get('general', 'Panel\Setting\GeneralController@index')->name('general');
                Route::post('general-update', 'Panel\Setting\GeneralController@generalUpdate')->name('generalUpdate');

                Route::resource('faq', 'Panel\Setting\FaqController');
                Route::post('faq-sortable', 'Panel\Setting\FaqController@sortable')->name('faq.sortable');

                Route::resource('provider', 'Panel\Setting\ProviderController');

                Route::resource('payment', 'Panel\Setting\PaymentController');
                Route::post('payment/update-status', 'Panel\Setting\PaymentController@updateStatus')->name('payment.updateStatus');
                Route::post('payment/edit', 'Panel\Setting\PaymentController@paymentEdit')->name('payment.paymentEdit');
                Route::post('payment/update', 'Panel\Setting\PaymentController@paymentUpdate')->name('payment.paymentUpdate');

                Route::get('module', 'Panel\Setting\ModuleController@index')->name('module');
                Route::post('module-update', 'Panel\Setting\ModuleController@update')->name('module.update');
                Route::post('module-edit', 'Panel\Setting\ModuleController@getModuleData')->name('module.edit');

                Route::resource('notification', 'Panel\Setting\NotificationController');
                Route::resource('staff-email', 'Panel\Setting\StaffEmailController');

                Route::resource('bonuses', 'Panel\Setting\BonusesController');

            });

        });
    });
});
