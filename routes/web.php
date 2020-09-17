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
      /*   Route::get('/orders', 'User\OrderController@orderLists'); */
  
        Route::post('statusChanges', 'User\OrderController@refillStatusChange')->name('user.changeRefillStatus');
        Route::post('/mass-order-store', 'User\OrderController@storeMassOrder')->name('massOrder.store');

    });

    Route::group(['prefix' => 'admin'], function () {
        // Authentication Routes...
        Route::get('/', 'Panel\Auth\LoginController@showLoginForm')->name('panel.login');
        Route::post('/login', 'Panel\Auth\LoginController@login')->name('panel.login.action');
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
            Route::post('orders/update/status', 'Panel\OrderController@bulkStatusChange');
            Route::get('get-orders', 'Panel\OrderController@getOrderLists');
            Route::post('orders/update/{id}', 'Panel\OrderController@updateOrder');
            Route::resource('orders', 'Panel\OrderController');

            #Drip-feed...
            Route::get('drip-feed-lists', 'Panel\DripFeedController@getDripFeedLists');
            Route::post('drip-feed/update/{id}', 'Panel\DripFeedController@updateDripOrder');
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
            Route::get('tasks-lists', 'Panel\TaskController@getTasksOrders');
            Route::post('refill/order/status', 'Panel\TaskController@refillChnageStatus')->name('task.change.status');
            Route::resource('tasks', 'Panel\TaskController');

            #Services...
            Route::resource('services', 'Panel\ServiceController');

            #Payments...
            Route::get('payments-lists', 'Panel\PaymentController@getPaymentLists');
            Route::resource('payments', 'Panel\PaymentController');

            #Tickets...
            Route::resource('tickets', 'Panel\TicketController');
            Route::get('tickets/status/{status}/{ticket_id}', 'Panel\TicketController@changeTicketStatus')->name('tickets.status.change');
            Route::post('tickets/status/changes', 'Panel\TicketController@changeBulkStatus')->name('tickets.status.changes');
            Route::post('tickets/{ticket}/comment', 'Panel\TicketController@comment')->name('tickets.comment');

            #Reports...
            Route::get('reports/profits', 'Panel\ReportController@profits');
            Route::get('reports/tickets', 'Panel\ReportController@ticket');
            Route::get('reports/orders', 'Panel\ReportController@order');
            Route::get('reports/payment', 'Panel\ReportController@payments');
            Route::resource('reports', 'Panel\ReportController');

            #Appearance...
            Route::group(['prefix' => 'appearance', 'as' => 'appearance.'], function () {
                Route::resource('page', 'Panel\Appearance\PageController');
                Route::post('page-status', 'Panel\Appearance\PageController@updateStatus')->name('page.updateStatus');

                Route::resource('menu', 'Panel\Appearance\MenuController');
                Route::post('menu-sortable', 'Panel\Appearance\MenuController@sortableMenu')->name('menu.sortable');

                Route::resource('file', 'Panel\Appearance\FileController');
            });

            #Appearance Themes...
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


    Route::get('/', 'Web\PageController@index')->name('home');
    Auth::routes(['verify' => true]);
    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::get('/dashboard', 'User\DashboardController@index')->name('dashboard');
    });
    Route::post('ticket/store', 'Web\TicketController@store')->name('ticket.store');      
    Route::post('/make_new_order', 'User\OrderController@store')->name('make.single.order');
    Route::get('/{url}', 'Web\PageController@page')->name('route');

    /* payment gateways  */
    Route::post('/payment/add-funds/paypal', 'User\PaypalController@store');
    Route::get('/payment/add-funds/paypal/success', 'User\PaypalController@success');
    Route::get('/payment/add-funds/paypal/cancel', 'User\PaypalController@cancel');
    Route::post('/payment/add-funds/paypal/ipn', 'User\PaypalController@ipn');


    
    //Route::get('/payment/add-funds/bitcoin', 'User\CoinPaymentsController@showForm');
    Route::post('/payment/add-funds/bitcoin', 'User\CoinPaymentsController@store');
    Route::get('/payment/add-funds/bitcoin/cancel', 'User\CoinPaymentsController@cancel');
    Route::get('/payment/add-funds/bitcoin/success', 'User\CoinPaymentsController@success');
    Route::post('/payment/add-funds/bitcoin/bit-ipn', 'User\CoinPaymentsController@ipn');

       // Authentication Routes...
 /*       Route::post('/login', 'Web\Auth\LoginController@login')->name('web.login.action');
       Route::post('logout', 'Panel\Auth\LoginController@logout')->name('panel.logout');

       Route::get('/register', 'Panel\Auth\RegisterController@showRegistrationForm')->name('panel.register');
       Route::post('/register', 'Panel\Auth\RegisterController@register'); */

});
