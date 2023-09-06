<?php

use Np\Contents\Models\Menu;
use Np\Structure\Classes\Jobs\CloneBanner;
use Np\Structure\Classes\Jobs\CloneFiles;
use Np\Structure\Classes\Jobs\CloneMenu;
use Np\Structure\Classes\Jobs\CloneServiceBox;
use Np\Structure\Classes\NP;
use Np\Structure\Facades\Oisf;
use Np\Structure\Models\Site;

Route::get('/backend/test', function () {
});


Route::get('/test', function () {
  $json = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
  $str = '{
        "list": {
          "title": "list.notice.title",
          "sortOrder": [
            {
              "name": "publish_date",
              "direction": "desc"
            }
          ],
          "toolbar": {
            "search": {
              "enable": true,
              "columns": [
                "title"
              ],
              "mode": "exact"
            }
          },
          "columns": [
            {
              "name": "title",
              "displayName": "column.title",
              "type": "text",
              "link": true
            },
            {
              "name": "publish_date",
              "displayName": "column.date.publish",
              "type": "date"
            },
            {
              "name": "attachments",
              "displayName": "column.attachments",
              "type": "file"
            }
          ]
        },
        "details": {
          "columns": [
            {
              "name": "title",
              "displayName": "column.title",
              "type": "text",
              "link": true
            },
            {
              "name": "attachments",
              "displayName": "column.attachments",
              "type": "file"
            },
            {
              "name": "publish_date",
             "displayName": "column.date.publish",
              "type": "date"
            },
            {
              "name": "archive_date",
              "displayName": "column.date.archieve",
              "type": "date"
            }
          ]
        }
      }';

  $data = json_decode($str, true);
  echo '<pre>';
  print_r($data);

  //$str = json_encode($str);
  //return 
});



Route::get('/', function () {
  return redirect('backend');
});


Route::any('/sso/oisf', function () {

  $items = collect(Oisf::getOisfOfficeMinistry());
  dump($items->pluck('nameBn', 'id')->toArray());
});

// Route::any('/applogin', function () {

//     Route::get('oisf', 'Np\Structure\Controllers\OisfSso@ssologin')->name('oisf');
// });

Route::any('/sso/oisf/logout', function () {
  dd('logout page handler');
});

Route::group(['middleware' => ['web']], function () {
  Route::get('oisf', 'Np\Structure\Controllers\Oisf@ssologin')->name('oisf');
  Route::post('applogin', 'Np\Structure\Controllers\Oisf@applogin')->name('oisf.login');
  Route::get('oisflogout', 'Np\Structure\Controllers\Oisf@ssologout')->name('oisf.logout');
  Route::get('applogout', 'Np\Structure\Controllers\Oisf@applogout')->name('app.logout');
});
