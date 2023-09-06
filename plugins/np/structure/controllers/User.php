<?php

namespace Np\Structure\Controllers;

use Backend\Classes\Controller;
use Np\Structure\Classes\Oisf\AppLogoutRequest;

use Backend, BackendAuth, Redirect;

class User extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function logout()
    {
        $redirectUrl = 'backend';

        // clear oisf session if user is oisf user
        $user = BackendAuth::getUser();
        $isSSO = $user->is_sso;

        if ($isSSO) {

            $appLogoutRequest = new AppLogoutRequest();
            $redirectUrl = $appLogoutRequest->buildRequest();
        }

        // clear local site session
        if (BackendAuth::isImpersonator()) {
            BackendAuth::stopImpersonate();
        } else {
            BackendAuth::logout();
        }

        return ($isSSO) ? Redirect::to($redirectUrl) : Backend::redirect($redirectUrl);
    }
}
