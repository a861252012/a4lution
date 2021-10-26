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

    Route::get('account/profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
    Route::put('account/profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
    Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);

    Route::get('fee/upload', ['as' => 'fee.upload', 'uses' => 'FeeController@uploadView']);
    Route::post('fee/upload/file', ['as' => 'fee.upload.file', 'uses' => 'FeeController@uploadFile']);
    Route::get('fee/platformads', ['as' => 'fee.platformads', 'uses' => 'FeeController@platformAdsView']);
    Route::get('fee/amzdaterange', ['as' => 'fee.amzdaterange', 'uses' => 'FeeController@amzDaterangeView']);
    Route::get('fee/monthlystorage', ['as' => 'fee.monthlystorage', 'uses' => 'FeeController@monthlyStorageView']);
    Route::get('fee/longtermstorage', ['as' => 'fee.longtermstorage', 'uses' => 'FeeController@longTermStorageView']);
    Route::get('fee/firstmileshipment', [
        'as' => 'fee.firstmileshipment',
        'uses' => 'FeeController@firstMileShipmentView'
    ]);
    Route::get('fee/export/{export_type}', ['as' => 'fee.export', 'uses' => 'FeeController@exportSampleFile']);
    Route::get('fee/checkIfReportExist/{report_date}', [
        'as' => 'fee.checkMonthlyReportExist',
        'uses' => 'FeeController@checkIfMonthlyReportExist'
    ]);

    Route::post('orders/edit', ['as' => 'orders.edit', 'uses' => 'ErpOrdersController@editOrders']);
    Route::put('orders/orderDetail/{id}', [
        'as' => 'orders.editOrderDetail',
        'uses' => 'ErpOrdersController@editOrderDetail'
    ]);
    Route::post('orders/checkEditQualification', [
        'as' => 'orders.checkEditQualification',
        'uses' => 'ErpOrdersController@checkEditQualification'
    ]);
    Route::post('orders/checkRate', ['as' => 'orders.checkRate', 'uses' => 'ErpOrdersController@checkRate']);

    Route::get('invoice/list', ['as' => 'invoice.list', 'uses' => 'InvoiceController@listView']);
    Route::get('invoice/issue', ['as' => 'invoice.issue', 'uses' => 'InvoiceController@issueView']);
    Route::delete('invoice/issue/{type}/{condition}', [
        'as' => 'invoice.delete',
        'uses' => 'InvoiceController@deleteIssue']);
    Route::delete('invoice/{id}', ['as' => 'invoice.deleteByID', 'uses' => 'InvoiceController@deleteInvoice']);

    Route::get('invoice/download/{token?}', ['as' => 'invoice.download', 'uses' => 'InvoiceController@downloadFile']);
    Route::post('invoice/checkIfReportExist', [
        'as' => 'invoice.checkIfReportExist',
        'uses' => 'InvoiceController@checkIfReportExist'
    ]);
    Route::post('invoice/edit', ['as' => 'invoice.edit', 'uses' => 'InvoiceController@editView']);
    Route::post('invoice/runReport/{store?}', ['as' => 'invoice.runReport', 'uses' => 'InvoiceController@runReport']);

    Route::get('{page}', ['as' => 'page.index', 'uses' => 'PageController@index']);

    Route::get('refund/search', ['as' => 'refund.search', 'uses' => 'ErpOrdersController@refundSearchView']);
    Route::get('orders/search', ['as' => 'orders.search', 'uses' => 'ErpOrdersController@ordersSearchView']);

    Route::get('employee/commissionpay', [
        'as' => 'employee.commissionPay',
        'uses' => 'EmployeeController@commissionPayView'
    ]);

    Route::get('employee/commissionpay/detail/{userID?}/{date?}', [
        'as' => 'employee.commissionDetail',
        'uses' => 'EmployeeController@commissionDetail'
    ]);

    Route::get('fee/extraordinaryitem', ['as' => 'fee.extraordinaryitem', 'uses' => 'FeeController@extraordinaryItem']);

    Route::post(
        'fee/extraordinaryitem',
        [
            'as' => 'create.fee.extraordinaryitem',
            'uses' => 'FeeController@createExtraordinaryItem'
        ]
    );

    Route::put(
        'fee/extraordinaryitem',
        [
            'as' => 'edit.fee.extraordinaryitem',
            'uses' => 'FeeController@editExtraordinaryItem'
        ]
    );

    Route::delete(
        'fee/extraordinaryitem/{id?}',
        [
            'as' => 'delete.extraordinaryitem',
            'uses' => 'FeeController@deleteExtraordinaryItem'
        ]
    );

    Route::put(
        'fee/extraordinaryitem/detail/{id}',
        [
            'as' => 'update.fee.extraordinaryitem.detail',
            'uses' => 'FeeController@updateExtraordinaryDetail'
        ]
    );

    Route::post(
        'fee/extraordinaryitem/createItem',
        [
            'as' => 'fee.extraordinaryitem.createItem',
            'uses' => 'FeeController@extraordinaryCreate'
        ]
    );

    Route::get('fee/clientCodeList', ['as' => 'fee.clientCodeList', 'uses' => 'FeeController@getClientCodeList']);

    Route::get('fee/allCurrency', ['as' => 'fee.allCurrency', 'uses' => 'FeeController@getAllCurrency']);

    Route::get('admin/approvaladmin', ['as' => 'admin.adminView', 'uses' => 'AdminController@approvalAdminView']);

    Route::put(
        'admin/approvaladmin/batch/{date}',
        [
            'as' => 'admin.batchApprove', 'uses' => 'AdminController@batchApprove'
        ]
    );

    Route::put(
        'admin/approvaladmin/revoke/{date}',
        [
            'as' => 'admin.RevokeApprove', 'uses' => 'AdminController@revokeApprove'
        ]
    );
});
