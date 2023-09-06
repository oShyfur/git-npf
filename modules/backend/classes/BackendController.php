<?php namespace Backend\Classes;

use Mail;
use Str;
use App;
use File;
use View;
use Event;
use Config;
use Request;
use Response;
use Closure;
use Illuminate\Routing\Controller as ControllerBase;
use October\Rain\Router\Helper as RouterHelper;
use System\Classes\PluginManager;
use ftp_connect;

use Illuminate\Support\Facades\DB;
use Np\Structure\Models\Domain;
use Np\Structure\Models\Site;

//
use Backend\Facades\BackendAuth;
use Np\Structure\Classes\SiteSessionData;
use Illuminate\Support\Facades\Session;
use Np\Structure\Classes\PhoneSms;

// use Illuminate\Support\Facades\Request;


use Validator;
use ApplicationException;
use ValidationException;



use Np\Structure\Models\Directorate;
use Illuminate\Support\Facades\Input;

/**
 * This is the master controller for all back-end pages.
 * All requests that are prefixed with the backend URI pattern are sent here,
 * then the next URI segments are analysed and the request is routed to the
 * relevant back-end controller.
 *
 * For example, a request with the URL `/backend/acme/blog/posts` will look
 * for the `Posts` controller inside the `Acme.Blog` plugin.
 *
 * @see Backend\Classes\Controller Base class for back-end controllers
 * @package october\backend
 * @author Alexey Bobkov, Samuel Georges
 */
class BackendController extends ControllerBase
{
    use \October\Rain\Extension\ExtendableTrait;

    /**
     * @var array Behaviors implemented by this controller.
     */
    public $implement;

    /**
     * @var string Allows early access to page action.
     */
    public static $action;

    /**
     * @var array Allows early access to page parameters.
     */
    public static $params;

    /**
     * @var boolean Flag to indicate that the CMS module is handling the current request
     */
    protected $cmsHandling = false;

    /**
     * Stores the requested controller so that the constructor is only run once
     *
     * @var Backend\Classes\Controller
     */
    protected $requestedController;

    /**
     * Instantiate a new BackendController instance.
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Process the request before retrieving controller middleware, to allow for the session and auth data
            // to be made available to the controller's constructor.
            $response = $next($request);

            // Find requested controller to determine if any middleware has been attached
            $pathParts = explode('/', str_replace(Request::root() . '/', '', Request::url()));
            if (count($pathParts)) {
                // Drop off preceding backend URL part if needed
                if (!empty(Config::get('cms.backendUri', 'backend'))) {
                    array_shift($pathParts);
                }
                $path = implode('/', $pathParts);

                $requestedController = $this->getRequestedController($path);
                if (!is_null($requestedController) && count($requestedController['controller']->getMiddleware())) {
                    $action = $requestedController['action'];

                    // Collect applicable middleware and insert middleware into pipeline
                    $controllerMiddleware = collect($requestedController['controller']->getMiddleware())
                        ->reject(function ($data) use ($action) {
                            return static::methodExcludedByOptions($action, $data['options']);
                        })
                        ->pluck('middleware');

                    foreach ($controllerMiddleware as $middleware) {
                        $middleware->call($requestedController['controller'], $request, $response);
                    }
                }
            }

            return $response;
        });

        $this->extendableConstruct();
    }

    /**
     * Extend this object properties upon construction.
     */
    public static function extend(Closure $callback)
    {
        self::extendableExtendCallback($callback);
    }

    /**
     * Pass unhandled URLs to the CMS Controller, if it exists
     *
     * @param string $url
     * @return Response
     */
    protected function passToCmsController($url)
    {
        if (
            in_array('Cms', Config::get('cms.loadModules', [])) &&
            class_exists('\Cms\Classes\Controller')
        ) {
            $this->cmsHandling = true;
            return App::make('Cms\Classes\Controller')->run($url);
        } else {
            return Response::make(View::make('backend::404'), 404);
        }
    }

    /**
     * Finds and serves the requested backend controller.
     * If the controller cannot be found, returns the Cms page with the URL /404.
     * If the /404 page doesn't exist, returns the system 404 page.
     * @param string $url Specifies the requested page URL.
     * If the parameter is omitted, the current URL used.
     * @return string Returns the processed page content.
     */
    public function run($url = null)
    {
        $params = RouterHelper::segmentizeUrl($url);

        // Handle NotFoundHttpExceptions in the backend (usually triggered by abort(404))
        Event::listen('exception.beforeRender', function ($exception, $httpCode, $request) {
            if (!$this->cmsHandling && $exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                return View::make('backend::404');
            }
        }, 1);

        /*
         * Database check
         */
        if (!App::hasDatabase()) {
            return Config::get('app.debug', false)
                ? Response::make(View::make('backend::no_database'), 200)
                : $this->passToCmsController($url);
        }

        $controllerRequest = $this->getRequestedController($url);
        if (!is_null($controllerRequest)) {
            return $controllerRequest['controller']->run(
                $controllerRequest['action'],
                $controllerRequest['params']
            );
        }

        /*
         * Fall back on Cms controller
         */
        return $this->passToCmsController($url);
    }

    /**
     * Determines the controller and action to load in the backend via a provided URL.
     *
     * If a suitable controller is found, this will return an array with the controller class name as a string, the
     * action to call as a string and an array of parameters. If a suitable controller and action cannot be found,
     * this method will return null.
     *
     * @param string $url A URL to determine the requested controller and action for
     * @return array|null A suitable controller, action and parameters in an array if found, otherwise null.
     */
    protected function getRequestedController($url)
    {
        $params = RouterHelper::segmentizeUrl($url);

        /*
         * Look for a Module controller
         */
        $module = $params[0] ?? 'backend';
        $controller = $params[1] ?? 'index';
        self::$action = $action = isset($params[2]) ? $this->parseAction($params[2]) : 'index';
        self::$params = $controllerParams = array_slice($params, 3);
        $controllerClass = '\\' . $module . '\Controllers\\' . $controller;
        if ($controllerObj = $this->findController(
            $controllerClass,
            $action,
            base_path() . '/modules'
        )) {
            return [
                'controller' => $controllerObj,
                'action' => $action,
                'params' => $controllerParams
            ];
        }

        /*
         * Look for a Plugin controller
         */
        if (count($params) >= 2) {
            list($author, $plugin) = $params;

            $pluginCode = ucfirst($author) . '.' . ucfirst($plugin);
            if (PluginManager::instance()->isDisabled($pluginCode)) {
                return Response::make(View::make('backend::404'), 404);
            }

            $controller = $params[2] ?? 'index';
            self::$action = $action = isset($params[3]) ? $this->parseAction($params[3]) : 'index';
            self::$params = $controllerParams = array_slice($params, 4);
            $controllerClass = '\\' . $author . '\\' . $plugin . '\Controllers\\' . $controller;
            if ($controllerObj = $this->findController(
                $controllerClass,
                $action,
                plugins_path()
            )) {
                return [
                    'controller' => $controllerObj,
                    'action' => $action,
                    'params' => $controllerParams
                ];
            }
        }

        return null;
    }

    /**
     * This method is used internally.
     * Finds a backend controller with a callable action method.
     * @param string $controller Specifies a method name to execute.
     * @param string $action Specifies a method name to execute.
     * @param string $inPath Base path for class file location.
     * @return ControllerBase Returns the backend controller object
     */
    protected function findController($controller, $action, $inPath)
    {
        if (isset($this->requestedController)) {
            return $this->requestedController;
        }

        /*
         * Workaround: Composer does not support case insensitivity.
         */
        if (!class_exists($controller)) {
            $controller = Str::normalizeClassName($controller);
            $controllerFile = $inPath . strtolower(str_replace('\\', '/', $controller)) . '.php';
            if ($controllerFile = File::existsInsensitive($controllerFile)) {
                include_once $controllerFile;
            }
        }

        if (!class_exists($controller)) {
            return $this->requestedController = null;
        }

        $controllerObj = App::make($controller);

        if ($controllerObj->actionExists($action)) {
            return $this->requestedController = $controllerObj;
        }

        return $this->requestedController = null;
    }

    /**
     * Process the action name, since dashes are not supported in PHP methods.
     * @param string $actionName
     * @return string
     */
    protected function parseAction($actionName)
    {
        if (strpos($actionName, '-') !== false) {
            return camel_case($actionName);
        }

        return $actionName;
    }

    /**
     * Determine if the given options exclude a particular method.
     *
     * @param string $method
     * @param array $options
     * @return bool
     */
    protected static function methodExcludedByOptions($method, array $options)
    {
        return (isset($options['only']) && !in_array($method, (array)$options['only'])) ||
            (!empty($options['except']) && in_array($method, (array)$options['except']));
    }

    public function getDomain()
    {
        $sitedata = Site::where('layer_id', 4)->select('id', 'name')->where('parent_id', 1)->where('ministry_id', 5)->get();

        return ['success' => true, 'sitedata' => $sitedata];
    }

    public function getSileDetails(Request $request)
    {
        $site = Site::where('id', $_GET['parent_id'])->first();
        $sitedata = Site::where('layer_id', $site->layer_id + 1)->select('id', 'name')->where('parent_id', $_GET['parent_id'])->get();
        $govtsitedata = Site::where('layer_id', $site->layer_id)->select('id', 'name')->where('parent_id', $_GET['parent_id'])->get();

        return ['success' => true, 'sitedata' => $sitedata, 'govtSiteData' => $govtsitedata];
    }

    public function update_content_type($table,$id){
        $cluster = Session::get('cluster');
        $key = 'database.connections.tenant';
        $db = config($key);
        $db['host'] = $cluster['host'];
        $db['username'] = $cluster['username'];
        $db['password'] = $cluster['password'];
        $site = Session::get('site');
        $db['database'] = $site['database'];
        config(
            [$key => $db]
        );
        $data = DB::connection('tenant')->table($table)->select('updated_at','id')->where('site_id',$site['id'])->orderBy('updated_at','desc')->first();
        $updata['updated_at'] = date('Y-m-d H:i:s');
        $status = DB::connection('tenant')->table($table)->where('id',$data->id)->update($updata);
        $data = DB::connection('tenant')->table($table)->select('updated_at','id')->where('site_id',$site['id'])->orderBy('updated_at','desc')->first();


        $contentLastUpdatelist = Session::get('site.resources.contentLastUpdate');
        $contentLastUpdate = array();
        foreach ($contentLastUpdatelist as $key => $value) {
            $contentLastUpdate[$key]['id'] = $value['id'];
            $contentLastUpdate[$key]['name'] = $value['name'];
            $contentLastUpdate[$key]['tableName'] = $value['tableName'];
            $contentLastUpdate[$key]['frequency'] = $value['frequency'];
            if ($table == $value['tableName']) {
                $contentLastUpdate[$key]['updated_at'] = $updata['updated_at'];
                $contentLastUpdate[$key]['diff_days'] = 0;
                $contentLastUpdate[$key]['over_date'] = 0;
                $contentLastUpdate[$key]['in_date']   = $value['frequency'];
            }else{
                $contentLastUpdate[$key]['updated_at'] = $value['updated_at'];
                $contentLastUpdate[$key]['diff_days'] = $value['diff_days'];
                $contentLastUpdate[$key]['over_date'] = $value['over_date'];
                $contentLastUpdate[$key]['in_date']   = $value['in_date'];
            }
            $contentLastUpdate[$key]['today_at'] = $value['today_at'];
        }
        Session::put('site.resources.contentLastUpdate',$contentLastUpdate);

        $contentLastUpdatelist = Session::get('site.resources.contentLastUpdate');
        //get content type
        $siteData = Site::where('id',$site['id'])->first();
        $data = SiteSessionData::getResource($siteData, 'content_types');
        // dd($contentLastUpdatelist);
        return redirect()->back();
    }


    // public function getContentTypes($id){
    //     return 1;
    // }

    public function ftp_sanwarul()
    {
        $ftp_server = getenv('CDN_HOST');
        $ftp_user_name = getenv('CDN_USERNAME');
        $ftp_user_pass = getenv('CDN_PASSWORD');

        // $ftp_server = '117.58.243.83';
        // $ftp_user_name = 'ittefaq@local.ftp';
        // $ftp_user_pass = 'ittefaq321#@1';

        $dir = "npfiles";
        // Where I need to put file
        // $local_file =getenv('LOCAL_FILE');
        $local_file = '/var/www/app/ctg-problem.pdf';
        // connect and login to FTP server

        $ftp_conn = ftp_connect($ftp_server, 21) or die("Could not connect to $ftp_server");
        $login = ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass);
        ftp_pasv($ftp_conn, true);
        if ($login) {
            // // print_r('connected<br>');
            // echo $_SERVER['DOCUMENT_ROOT'];
            // die();

            // Creating directory
            if (ftp_mkdir($ftp_conn, $dir)) {
                // Where I copy the file
                $remote_file = $dir . "/ctg-problem.pdf";
                // Execute if directory created successfully
                print_r(" $dir Successfully created");

                // upload a file
                if (ftp_put($ftp_conn, $remote_file, $local_file, FTP_ASCII)) {
                    print_r("successfully uploaded $local_file\n");
                    exit;
                } else {
                    print_r("There was a problem while uploading $local_file\n");
                    exit;
                }
            } else {

                // Execute if fails to create directory
                print_r("Error while creating $dir");
            }

        }
        ftp_close($ftp_conn);
    }

    public function checkTwoStepAuthentication(): \Illuminate\Http\JsonResponse
    {
        $login = $_POST['login'];
        $password = $_POST['password'];
        $data['login'] = $login;
        $data['password'] = $password;
        $rules = [
            'login' => 'required|between:2,255',
            'password' => 'required|between:4,255'
        ];

        $validation = Validator::make($data, $rules);
        if ($validation->fails()) return response()->json(['error' => 'Your username and password does not match']);
        $login = $this->user_login($login);

        try {
            BackendAuth::attempt(['login' => $login, 'password' => $_POST['password']]);
            $user = DB::table('backend_users')->select('secure_login')->where('login', $login)->first();
            if (empty($user)) return response()->json(['error' => 'Your username and password does not match']);
            if ($user->secure_login == 1) $secure_login = 'yes';
            else $secure_login = 'no';
            return response()->json(['success' => '2 step authentication', 'secure_login' => $secure_login]);
        } catch (\October\Rain\Auth\AuthException $e) {
            return response()->json(['error' => 'Your username and password does not match']);
        }
    }

    public function send_otp(): \Illuminate\Http\JsonResponse
    {
        $login = $_POST['login'];
        $data['otp'] = rand(000000, 999999);

        $login = $this->user_login($login);

        try {
            $user = DB::table('backend_users')->select('login', 'email', 'phone')->where('login', $login)->first();
            if (empty($user)) return response()->json(['error' => 'A user was not found with the given credentials.']);
            if (isset($_POST['medium']) && $_POST['medium'] == "phone") {
                preg_match("/^(?:\\+88|88)?(01[3-9]\\d{8})/", $user->phone, $result);
                if (!$result) return response()->json(['error' => 'Your mobile number is not a valid number.']);
                $phone = $result[1];
                $mask_address = str_repeat("*", 8) . substr($phone, -3);
                $message = 'Your verification code ' . $data['otp'] . ' Please do not share your OTP or PIN with others.';
                PhoneSms::send($message, $phone);
            } else {
                Mail::send('backend::mail.otp', $data, function ($message) use ($user) {
                    $message->to($user->email, 'Your OTP')->subject('Send verification code');
                });
                $mask_address = $this->mask_email($user->email);
            }
            DB::table('backend_user_otp')->updateOrInsert(['login' => $login], [
                'login' => $login,
                'otp' => $data['otp'],
                'attempt_count' => 0,
                'created_at' => DB::raw('NOW()'),
            ]);
            return response()->json(['success' => 'OTP Send', 'otp' => 'Please Check Your Email', 'mail_address' => $mask_address]);
        } catch (\October\Rain\Auth\AuthException $e) {
            return response()->json(['error' => 'A user was not found with the given credentials.']);
        }
    }

    public function checkValidateOtp(): \Illuminate\Http\JsonResponse
    {
        $login = $_POST['login'];
        $otp = $_POST['otp'];
        $postUser = $this->user_login($login);

        $otp_check = DB::table('backend_user_otp')->where('login', $postUser)->first();

        if(empty($otp_check)){
          return response()->json(['response_type' => false, 'data' => 'invalid_login']);
        }

        if((int)$otp_check->otp === (int)$otp){
            return response()->json(['response_type' => true, 'data' => 'valid_login']);
        }else {
            if($otp_check->attempt_count === 0){
                DB::table('backend_user_otp')->where('login', $postUser)->update(['attempt_count' => 1]);
            }else if($otp_check->attempt_count === 1){
                DB::table('backend_user_otp')->where('login', $postUser)->update(['attempt_count' => 2]);
            }else if($otp_check->attempt_count === 2){
                DB::table('backend_user_otp')->where('login', $postUser)->update(['attempt_count' => 3]);
            }else{
                DB::table('backend_user_otp')->where('login', $postUser)->delete();
                return response()->json(['response_type' => false, 'data' => 'invalid_attempt']);
            }
            return response()->json(['response_type' => false, 'data' => 'invalid_otp']);
        }
    }

    private function mask_email($email): string
    {
        $mail_parts = explode("@", $email);
        $username = $mail_parts[0];
        $len = strlen($username);
        $mail_parts[0] = substr($username, 0, 1)
            . str_repeat("*", 6)
            . substr($username, $len - 3, 3);
        return implode("@", $mail_parts);
    }

    private function user_login($postUser)
    {
        $login = $postUser;
        if (filter_var($postUser, FILTER_VALIDATE_EMAIL)) {
            $userData = DB::table('backend_users')->select('login')->where('email', $postUser)->first();
            if (!empty($userData)) $login = $userData->login;
        }
        return $login;
    }


    public function getDirectorates()
    {

        $ministryId = Input::get('ministry_id'); // Fetch the selected ministry_id from the AJAX request

        // Filter directorates based on the selected ministry_id
        $directorates = Directorate::where('ministry_id', $ministryId)->get();

        // Return the filtered directorates as JSON response
        return response()->json($directorates);
    }

}
