<?php

namespace Np\Structure\Controllers;

use Backend, BackendAuth, Flash;
use Backend\Classes\Controller;
use Backend\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

use Np\Structure\Classes\Oisf\AppLoginRequest;
use Np\Structure\Classes\Oisf\AppLoginResponse;
use Np\Structure\Classes\Oisf\AppLogoutRequest;
use Np\Structure\Facades\Oisf as NpOisf;

/**
 * Oisf Back-end Controller
 */

class Oisf extends Controller
{
    protected $publicActions = ['ssologin', 'applogin', 'logout'];

    public function __construct()
    {
        parent::__construct();
        //$this->oisf_helper = new OisfApiHelper();
    }
    public function logout()
    {
        dd('logout');
    }

    public function ssologin(Request $request)
    {
        $appLoginRequest = new AppLoginRequest();
        $requestUrl = $appLoginRequest->buildRequest();
        $nonce = $appLoginRequest->getReqNonce();
        $request->session()->put("nonce", $nonce);
        return Redirect::to($requestUrl);
    }

    public function applogin(Request $request)
    {     

       

        try {

            $token = $request->input('token');
            $nonce = session()->get("nonce");
            $appLoginResponse = new AppLoginResponse();
            $response = $appLoginResponse->parseResponse($token, $nonce);
            $request->session()->forget("nonce");
        
            $employee_record_id = $response->getEmployeeRecordId();
            $username = $response->getUserName();

            // sso for mopa private content feature
            //https://mofa.gov.bd/site/view/private_contents

            $ssoValue = $username.'-'.$employee_record_id;
            $mofaUrl = 'https://mofa.gov.bd/site/view/private_contents?sso='.$ssoValue;
            return Redirect::to($mofaUrl);
                
            // end mofa setting

            

            $user = null;
            // check user existance
            if ($username or $employee_record_id) {
                $user = User::where('is_sso', 1)->where(function ($q) use ($username, $employee_record_id) {

                    $q->where('login', $username)->orWhere('login', $employee_record_id);
                })->first();
            }

            if ($user) {
                // valid user, update information

                $employee = NpOisf::getOisfEmployee($employee_record_id);
                $employee = $employee[0];

                $user->login = $response->getUserName();
                $user->designation = $response->getDesignation();
                $user->first_name = $employee['name'] ?: $employee['nameBn'];
                $user->phone = $employee['mobile'];

                //if email exist already, don't update email
                if (!User::where('email', $employee['email'])->first())
                    $user->email = $employee['email'];

                $user->save();

                // login and redirecto user to dashboard
                BackendAuth::login($user);
                Flash::success('Logged in successfully!');
                return Backend::redirect('backend');
            }

            Flash::error('Something wrong happended');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            Flash::error($msg);
        }
        return Backend::redirect('backend');
    }

    public function ssologout(Request $request)
    {
        // Building request from sso url
        // $appLogoutRequest = new AppLogoutRequest();
        // $requestUrl = $appLogoutRequest->buildRequest();

        // Log out from application
        BackendAuth::logout();

        //redirect to mofa
        $mofaUrl = 'https://mofa.gov.bd/site/view/private_contents?logout=true';
        return Redirect::to($mofaUrl);
                

        //return Redirect::to($requestUrl);
    }
    public function applogout(Request $request)
    {
       // Building request from sso url
       $appLogoutRequest = new AppLogoutRequest();
       $requestUrl = $appLogoutRequest->buildRequest();

       // Log out from application
       BackendAuth::logout();
             

       return Redirect::to($requestUrl);
    }
}
