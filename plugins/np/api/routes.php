<?php

use System\Models\MailTemplate;
use Np\Api\Twig\ThemeManager;
use Illuminate\Support\Facades\Session;
use Np\Structure\Classes\NP;
use Np\Structure\Models\Site;


function makeSlug($string)
{
    $string = trim($string, ",");
    return preg_replace('/\s+/u', '-', trim($string));
}
Route::group([
    'prefix' => 'api/v1/theme/',
    'middleware' => ['Np\Api\Middleware\InitializedTenantDB', 'Np\Api\Middleware\Cors']
], function () {

    Route::get('form/{form_code}', 'Np\Api\Controllers\ThemeController@form');
    Route::get('home', 'Np\Api\Controllers\ThemeController@home');
    Route::get('list/{view_code}', 'Np\Api\Controllers\ThemeController@list');
    Route::get('employee_list/{view_code}/{slug}', 'Np\Api\Controllers\ThemeController@dynamicEmployeeList');
    Route::get('{view_code}/{slug}', 'Np\Api\Controllers\ThemeController@details');
    
    Route::match(['get', 'post'], 'ajax', 'Np\Api\Controllers\AjaxController@ajax');


    
    Route::match(['get', 'post'],'lists/data/{end_point}', 'Np\Api\Controllers\ThemeController@create_content');

    // Route::post('nothi/list', 'Np\Api\Controllers\ThemeController@nothi');
    Route::post('nothi/list/notice', 'Np\Api\Controllers\ThemeController@nothiNotice')->name('nothi.list.notice');
    Route::post('nothi/list/news', 'Np\Api\Controllers\ThemeController@nothiNews')->name('nothi.list.news');
});



// Route::group([
//     'prefix' => '/api',
//     'middleware' => ['Np\Api\Middleware\InitializedTenantDB', 'Np\Api\Middleware\Cors']
// ], function () {

//     Route::post('/CreateContentType', 'Np\Api\Controllers\NothiAPIIntegrationsController@CreateContent');
// });
