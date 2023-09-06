<?php

Route::namespace('Np\Contents\Controllers')->middleware(['web', 'Np\Structure\Middleware\InitializedTenantDB'])->group(function () {

    //Route::get('np/contents/contenttype', '')->name('oisf');

});
