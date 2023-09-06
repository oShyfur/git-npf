<?php

namespace Np\Structure\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Http\Response;
use October\Rain\Exception\AjaxException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Session;
use BackendAuth;
use Backend\Models\User;
use Laralab\MultiDomain\Models\Site;

class InitializedTenantFileSystem
{
    /**
     * The Laravel Application
     *
     * @var Application
     */
    protected $app;

    /**
     * Create a new middleware instance.
     *
     * @param  Application $app
     * @return void
     */
    public function __construct()
    {
        //$this->app = $app;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {

        $this->initFileSystem();
        return $next($request);
    }

    public function initFileSystem()
    {
        $tenant = session('site.uuid') ?: 'central';

        $mediaStorage = config('cms.storage.media');
        $mediaStorage['folder'] = $mediaStorage['folder'] . '/' . $tenant;
        $mediaStorage['path'] = $mediaStorage['path'] . '/' . $mediaStorage['folder'];
        config(
            [
                'cms.storage.media' =>
                $mediaStorage
            ]
        );
    }
}
