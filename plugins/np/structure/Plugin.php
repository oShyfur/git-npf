<?php

namespace Np\Structure;

use App, Event;
use System\Classes\PluginBase;
use Illuminate\Routing\Router;
use Np\Structure\Middleware\InitializedTenantFileSystem;
use Np\Structure\Classes\BackendUserExtension;
use Np\Structure\Classes\EventsHandler;
use Np\Structure\Classes\BuilderPluginExtension;
use Np\Structure\Classes\SystemTemplateExtension;
use Np\Structure\Classes\MacroExtension;
use Illuminate\Support\Facades\DB;
use Np\Structure\Classes\BackendAuthLoginExtension;
use Np\Structure\Classes\OisfApiHelper;
use Np\Structure\Classes\SystemFileExtension;
use Np\Structure\Classes\TranslationExtension;
use RainLab\Translate\Models\Locale;
use RainLab\Translate\Models\MyLocale;

class Plugin extends PluginBase
{

    public $elevated = true;

    public function pluginDetails()
    {
        return [
            'name' => 'NP Structure',
            'description' => 'Provide NP Architecture.',
            'author' => 'NP Team',
            'icon' => 'icon-leaf'
        ];
    }
    public function registerComponents()
    {
    }

    public function registerSettings()
    {
    }

    public function registerReportWidgets()
    {
        return [
            'Np\Structure\ReportWidgets\SiteSelect' => [
                'label'   => 'Site Selector Widget',
                'context' => 'dashboard',

            ]
        ];
    }

    public function register()
    {
        //$this->registerConsoleCommand('stucture.clonemenu', 'Np\Structure\Console\CloneMenu');
    }
    public function boot()
    {

        //enable sql loggin
        //trace_sql();


        //oisf api fecade binding

        App::bind('oisf.api', function () {
            return new OisfApiHelper;
        });

        $router = resolve(Router::class);
        $router->pushMiddlewareToGroup('web', InitializedTenantFileSystem::class);

        $this->extendClasses();
        $this->addAssets();
        Event::subscribe(new EventsHandler());
    }

    public function extendClasses()
    {
        (new BackendUserExtension)->boot();
        (new BuilderPluginExtension)->boot();
        (new SystemTemplateExtension)->boot();
        (new MacroExtension)->boot();
        (new SystemFileExtension)->boot();
        (new BackendAuthLoginExtension)->boot();
        (new TranslationExtension)->boot();
    }

    public function addAssets()
    {
        //add assets to all backend pages

        Event::listen('backend.page.beforeDisplay', function ($controller, $action, $params) {
            $controller->addJs('/plugins/np/structure/assets/js/app.js', '1.0.0');
        });

        Event::listen('data.getRecords', function ($offset, $total) {
            return [['id' => 1, 'drug' => 'John', 'man' => 'Smith'], ['id' => 2, 'drug' => 'John', 'man' => 'Doe'],];
        });
        Event::listen('data.getCount', function () {
            return 2;
        });

    }
}
