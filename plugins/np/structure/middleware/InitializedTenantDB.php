<?php

namespace Np\Structure\Middleware;

use Closure;
use Illuminate\Foundation\Application;
use October\Rain\Support\Facades\Flash;

class InitializedTenantDB
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
        if (!session('site.id')) {
            Flash::error('Pls select a site first');
            return redirect(config('cms.backendUri'));
        }
        $this->initDB();

        // add isTenant key to request
        $request->merge([
            'isTenant' => 1
        ]);

        return $next($request);
    }

    public function initDB()
    {
        $key = 'database.connections.tenant';
        $db = config($key);
        $db['host'] = session('cluster.host');
        $db['username'] = session('cluster.username');
        $db['password'] = session('cluster.password');;
        $db['database'] = session('site.database');;
        config(
            [$key => $db]
        );
    }
}
