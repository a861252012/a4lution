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

Route::get('/', 'Auth\LoginController@showLoginForm')->name('welcome');

Auth::routes();

Route::get('dashboard', 'HomeController@index')->name('home');
Route::get('pricing', 'PageController@pricing')->name('page.pricing');
Route::get('lock', 'PageController@lock')->name('page.lock');

Route::get('/test', 'TestController@test');

Route::group(['middleware' => 'auth'], function () {
    Route::resource('category', 'CategoryController', ['except' => ['show']]);
    Route::resource('tag', 'TagController', ['except' => ['show']]);
    Route::resource('item', 'ItemController', ['except' => ['show']]);
    Route::resource('role', 'RoleController', ['except' => ['show', 'destroy']]);
    Route::resource('user', 'UserController', ['except' => ['show']]);

    Route::prefix('account')->group(function () {
        Route::get('/profile', 'ProfileController@edit')->name('profile.edit');
        Route::put('/profile', 'ProfileController@update')->name('profile.update');
    });

    Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);

    Route::prefix('fee')->group(function () {
        Route::get('/upload', 'FeeController@uploadView')->name('fee.upload.view');
        Route::post('/upload/file', 'FeeController@uploadFile');
        Route::get('/platformads', 'FeeController@platformAdsView')->name('fee.platformAds.view');
        Route::get('/amzdaterange', 'FeeController@amzDateRangeView')->name('fee.amzDateRange.view');
        Route::get('/monthlystorage', 'FeeController@monthlyStorageView')->name('fee.monthlyStorage.view');
        Route::get('/longtermstorage', 'FeeController@longTermStorageView')->name('fee.longTermStorage.view');
        Route::get('/firstmileshipment', 'FeeController@firstMileShipmentView')->name('fee.firstMileShipment.view');
        Route::get('/export/{export_type}', 'FeeController@exportSampleFile');
        Route::post('/preValidation/{date}/{type}', 'FeeController@preValidation');

        Route::prefix('extraordinaryitem')->group(function () {
            Route::get('/', 'FeeController@extraordinaryItem')->name('fee.extraordinaryItem.view');
            Route::post('/', 'FeeController@createExtraordinaryItem');
            Route::put('/', 'FeeController@editExtraordinaryItem');
            Route::delete('/{id?}', 'FeeController@deleteExtraordinaryItem');
            Route::put('/detail/{id}', 'FeeController@updateExtraordinaryDetail');
            Route::post('/createItem', 'FeeController@extraordinaryCreate');
        });

        Route::get('/clientCodeList', 'FeeController@getClientCodeList');
        Route::get('/allCurrency', 'FeeController@getAllCurrency');
    });

    // ERP Orders
    Route::get('refund/search', 'ErpOrdersController@refundSearchView')->name('refundOrder.view');
    Route::get('orders/search', 'ErpOrdersController@ordersSearchView')->name('erpOrder.view');

    Route::prefix('orders')->group(function () {
        Route::post('/edit', 'ErpOrdersController@editOrders');
        Route::put('/orderDetail/{id}', 'ErpOrdersController@editOrderDetail');
        Route::post('/checkEditQualification', 'ErpOrdersController@checkEditQualification');
        Route::post('/checkRate', 'ErpOrdersController@checkRate');
        Route::get('/bulkUpdate/index', 'ErpOrdersController@bulkUpdateView')->name('bulkUpdate.view');
        Route::post('/bulkUpdate', 'ErpOrdersController@bulkUpdate');
        Route::post('/ajax/bulkUpdate', 'ErpOrdersController@ajaxValidateFileHeadingRow');
        Route::get('/exportSample', 'ErpOrdersController@exportSample')->name('orders.sample.download');
    });

    Route::prefix('invoice')->group(function () {
        Route::get('/list', 'InvoiceController@listView')->name('invoice.list.view');
        Route::get('/issue', 'InvoiceController@issueView')->name('invoice.issue.view');
        Route::delete('/issue/{type}/{condition}', 'InvoiceController@deleteIssue');
        Route::delete('/{id}', 'InvoiceController@deleteInvoice');

        Route::get('/download/{token?}', 'InvoiceController@downloadFile');
        Route::get('/validation/{date}/{clientCode}', 'InvoiceController@reportValidation')
            ->name('invoice.reportValidation');
        Route::post('/edit', 'InvoiceController@editView');
        Route::post('/createBill', 'InvoiceController@createBill')->name('invoice.createBill');
    });

    Route::prefix('ajax')->group(function () {
        Route::post('/billing-statements', 'BillingStatementController@ajaxStore')
            ->name('ajax.billing_statement.store');
        Route::post('/invoice/export', 'InvoiceController@ajaxExport')->name('ajax.invoice.export');
    });

    Route::prefix('employee')->group(function () {
        Route::get('/commissionpay', 'EmployeeController@commissionPayView')->name('employeeCommission.view');
        Route::get('/commissionpay/detail/{userID?}/{date?}', 'EmployeeController@commissionDetail');
    });

    Route::prefix('admin/approvaladmin')->group(function () {
        Route::get('/', 'AdminController@approvalAdminView')->name('admin.adminView');
        Route::put('/batch/{date}', 'AdminController@batchApprove');
        Route::put('/revoke/{date}', 'AdminController@revokeApprove');
    });

    Route::prefix('management')->group(function () {
        Route::get('/exchangeRate', 'ExchangeRateController@index')->name('exchangeRate.view');
        Route::post('/exchangeRate/create', 'ExchangeRateController@ajaxCreate');
        Route::get('/exchangeRate/{date}', 'ExchangeRateController@ajaxShow');
        Route::get('/exchangeRate/{currency}/{startDate}/{endDate}', 'ExchangeRateController@ajaxGetExchangeRate');
    });

    Route::get('/customers', 'CustomerController@index')->name('customer.index');
    Route::post('/ajax/customers/create', 'CustomerController@ajaxCreate')->name('ajax.customer.create');
    Route::post('/ajax/customers/store', 'CustomerController@ajaxStore')->name('ajax.customer.store');
    Route::post('/ajax/customers/{client_code}/edit', 'CustomerController@ajaxEdit')->name('ajax.customer.edit');
    Route::patch('/ajax/customers/{client_code}', 'CustomerController@ajaxUpdate')->name('ajax.customer.update');



    Route::get('{page}', 'PageController@index')->name('page.index');
});