<?php

namespace Np\Contents;

use System\Classes\PluginBase;
use Np\Contents\Classes\RevisionExtension;
use Np\Contents\Classes\EventsHandler;
use Illuminate\Support\Facades\Event;
use Np\Contents\Classes\ContentModelExtension;

class Plugin extends PluginBase
{
    public function registerComponents()
    { }

    public function registerSettings()
    { }

    public function registerFormWidgets()
    {
        return [
            'Np\Contents\FormWidgets\LinkPicker' => 'linkpicker',
            'Np\Contents\FormWidgets\TaxonomyPicker' => 'taxonomypicker',
        ];
    }
    public function boot()
    {

        $this->extendClasses();
        Event::subscribe(new EventsHandler());
    }

    public function extendClasses()
    {
        //(new ContentsControllerExtension)->boot();
    }
}
