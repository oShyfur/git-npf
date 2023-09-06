<?php namespace Np\Contents\FormWidgets;

use RainLab\Builder\Widgets\DefaultControlDesignTimeProvider;

class ControlDesignTimeProvider extends DefaultControlDesignTimeProvider
{
    public function __construct()
    {
        $this->defaultControlsTypes[] = 'taxonomypicker';
        $this->defaultControlsTypes[] = 'linkpicker';
    }
}
