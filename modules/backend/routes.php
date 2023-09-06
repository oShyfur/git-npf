<?php

/**
 * Register Backend routes before all user routes.
 */
App::before(function ($request) {
    /*
     * Extensibility
     */
    Event::fire('backend.beforeRoute');
    Route::get('ftp', 'Backend\Classes\BackendController@ftp_sanwarul');
    Route::get('getdomain', 'Backend\Classes\BackendController@getDomain');
    Route::get('getSileDetails', 'Backend\Classes\BackendController@getSileDetails');



    Route::group(['middleware' => ['web']], function () {
        //get directorate for ministry selected
        Route::get('/getDirectorates', 'Backend\Classes\BackendController@getDirectorates');

        Route::get('update/content/type/{table}/{id}', 'Backend\Classes\BackendController@update_content_type')->name('content_type');
    });

    // User login send otp and attempt check route
    Route::post('/backend/backend/auth/send/otp', 'Backend\Classes\BackendController@send_otp');
    Route::post('/backend/backend/auth/send/two-step-check', 'Backend\Classes\BackendController@checkTwoStepAuthentication');
    Route::post('/backend/backend/auth/send/check-validate-otp', 'Backend\Classes\BackendController@checkValidateOtp');
    /*
     * Other pages
     */
    Route::group([
            'middleware' => ['web'],
            'prefix' => Config::get('cms.backendUri', 'backend')
        ], function () {
            Route::any('{slug}', 'Backend\Classes\BackendController@run')->where('slug', '(.*)?');

        })
    ;

    /*
     * Entry point
     */
    Route::any(Config::get('cms.backendUri', 'backend'), 'Backend\Classes\BackendController@run')->middleware('web');

    /*
     * Extensibility
     */
    Event::fire('backend.route');
});
