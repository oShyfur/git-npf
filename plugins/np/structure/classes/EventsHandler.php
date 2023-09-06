<?php

namespace Np\Structure\Classes;

use Session;
use BackendAuth;
use Np\Structure\Models\Site;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Router;
use Backend\Facades\Backend;
use Flash;
use October\Rain\Exception\ValidationException;
use Redirect;

class EventsHandler
{

    public function subscribe($events)
    {

        //set session data after login
        $events->listen('backend.user.login', 'Np\Structure\Classes\EventsHandler@userLogin');
        // add middle to ini tenant db
        $events->listen('backend.beforeRoute', 'Np\Structure\Classes\EventsHandler@beforeRoute');

        // filter user list page
        $events->listen('backend.list.extendQuery', 'Np\Structure\Classes\BackendUserExtension@userListQueryExtend');

        // check content type permission
        $events->listen('backend.page.beforeDisplay', 'Np\Structure\Classes\EventsHandler@beforePageDisplay');
    }

    public function beforePageDisplay($controller, $action, $params)
    {
        $controllerName = explode('\\', get_class($controller));
        $requestedCt = strtolower(end($controllerName));
        $whiteListed = ['contenttype', 'taxonomytype', 'siteblock', 'menu', 'taxonomy', 'forms'];

        if ($controllerName[0] == 'Np' and $controllerName[1] == 'Contents' and !in_array($requestedCt, $whiteListed)) {

            $cts = session('site.resources.content_types', []);
            $cts = array_map('strtolower', collect($cts)->pluck('code')->toArray());

            if (!in_array($requestedCt, $cts)) {
                Flash::error('Access denied for requested page');
                return redirect(config('cms.backendUri') . '/np/contents/contenttype')->with('error', 'Access Denied');
            }
        }
    }
    /**
     * Handle user login events.
     */
    public function userLogin($user)
    {

        $this->setUserSession($user);

        if (!$user->adminLevelUser()) {

            $sites = $user->sites;

            if ($sites->count() == 0) {
                throw new ValidationException(['login' => ['Username is not assigned to any  website. Pls contact with site administrator'],]);
            }

            if ($sites->count() == 1) {
                $site = $sites->first();
                SiteSessionData::setSite($site);
            } else if ($user->getDefaultSite()->count()) {
                $site = $user->getDefaultSite()->first();
                SiteSessionData::setSite($site);
            }
        }
    }

    /**
     * Handle user logout events.
     */
    public function userLogout($user)
    {
    }


    public function beforeRoute()
    {


        $router = resolve(Router::class);
        $tenantUri  = ltrim(config('cms.backendUri') . '/np/contents/', '/');

        if (strpos(request()->path(), $tenantUri) !== false) {
            $router->pushMiddlewareToGroup('web', 'Np\Structure\Middleware\InitializedTenantDB');
        }
    }

    public function setUserSession($user)
    {

        $role = $user->role;
        $userData =
            [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'role' => $role->code
            ];
        Session::put('user', $userData);
    }
    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
}
