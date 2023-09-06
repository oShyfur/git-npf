<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Classes\HttpClient;

$router->get('user', function () {
    // return redirect('//stage-login.portal.gov.bd');
    return redirect('//login.'.str_replace('login.',''.$_SERVER['HTTP_HOST']));
});

$router->get('serverStatus', function () {
    return 'Welcome, National portal of bangladesh';
});

$router->group(['middleware' => 'site'], function () use ($router) {

    $getUri = $_SERVER['REQUEST_URI'];

    if(strpos($getUri,'<?php') || strpos($getUri,'?>') || strpos($getUri,'<script') || strpos($getUri,'/script>')){
        if(count($matches)>0)
        return abort(404);
    }

    $client = new HttpClient;

    $router->get('/[{lang:bn|en}]', function ($lang = null) use ($client) {

        $api = 'home';
        echo $client->send($api);
    });


    $router->get('/{lang}/site/view/{view_code:[a-zA-Z-_]{0,50}}/{slug}', function ($lang, $view_code, $slug) use ($client) {
        //dd($slug); 
        $api = 'employee_list/officer_list/'. $slug;
        echo $client->send($api);
    });
    
    $router->get('/{lang}/site/view/{view_code:[a-zA-Z-_]{0,50}}', function ($lang, $view_code) use ($client) {
        $api = 'list/' . $view_code;
        echo $client->send($api);
    });

    $router->get('/{lang}/site/{view_code:[a-zA-Z-_]{0,50}}/{slug}', function ($lang, $view_code, $slug) use ($client) {

        $api = $view_code . '/' . $slug;
        echo $client->send($api);
    });

    $router->get('/{lang}/form/{form_code:[a-zA-Z-_]{0,50}}', function ($lang, $form_code) use ($client) {

        $api = 'form/' . $form_code;
        echo $client->send($api);
    });




    // --------------------------------
    /*$router->post('/{lang}/nothi/site/view', function ($lang) use ($client) {
        $api = 'nothi/list';
        echo $client->post($api);
    });*/


    $router->post('/{lang}/get/content/type', function ($lang) use ($client) {
        $api = 'get/content/type';
        return $client->post($api);
    });

    $router->post('/{lang}/nothi/site/{type}', function ($lang,$type) use ($client) {
        if ($type == 'notice') {
            $api = 'nothi/list/notice';
        } elseif($type == 'news') {
            $api = 'nothi/list/news';
        }else{
            abort(404);
        }        
        echo  "<pre>";print_r($client->post($api));echo "</pre>";
    });
});
