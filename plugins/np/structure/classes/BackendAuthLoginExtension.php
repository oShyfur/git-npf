<?php

namespace Np\Structure\Classes;


use Event, View;
use Backend\Controllers\Auth;

/**
 * Class BackendUserExtension
 * @package Renatio\Logout\Classes
 */
class BackendAuthLoginExtension
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->extendLoginView();
        $this->extendAuthPartials();
    }



    public function ExtendLoginView()
    {
        Event::listen('backend.auth.extendSigninView', function ($controller) {
            return View::make("np.structure::oisf_login");
        });
    }

    public function extendAuthPartials()
    {
        //extend auth controller views files
        Auth::extend(function ($controller) {
            list($author, $plugin) = explode('\\', strtolower(get_class()));
            $partials_path = sprintf('$/%s/%s/controllers/auth', $author, $plugin);
            $controller->addViewPath($partials_path);
        });
    }
}
