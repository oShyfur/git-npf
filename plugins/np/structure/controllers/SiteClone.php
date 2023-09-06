<?php

namespace Np\Structure\Controllers;

use ApplicationException;
use Backend\Classes\Controller;
use BackendMenu;
use Np\Contents\Models\Menu;
use Np\Structure\Classes\NP;
use Np\Structure\Models\Domain;
use ValidationException;
use Validator;
use DB;
use Flash;
use Np\Structure\Classes\Jobs\CloneMenu;
use Np\Structure\Classes\SiteSessionData;
use Np\Structure\Models\SiteClone as ModelsSiteClone;

class SiteClone extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController'
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'manage_permission'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Structure', 'np-structure', 'side-menu-item');
    }

    public function onClone()
    {
        $inputs =  post('SiteClone');
        $validator = Validator::make(
            $inputs,
            [
                'source_site_id' => 'required',
                'destination_site_id' => 'required',
                'resources' => 'array',
            ],
            [
                'resources.array' => 'Select resources to be cloned'
            ]
        );
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $sourceDomain = Domain::with('site')->where('fqdn',  $inputs['source_site_id'])->first();
        $destinationDomain = Domain::with('site')->where('fqdn',  $inputs['destination_site_id'])->first();

        if (!$sourceDomain) {
            throw new ValidationException(['source_site_id' => 'Source Domain not found']);
        }

        if (!$destinationDomain) {
            throw new ValidationException(['destination_site_id' => 'Destination Domain not found']);
        }

        $sourceSite = $sourceDomain->site;
        $destinationSite = $destinationDomain->site;

        $done = [];
        foreach ($inputs['resources'] as $resource) {

            $className = "\\Np\\Structure\\Classes\\Jobs\\Clone" . ucfirst($resource);
            if (class_exists($className)) {
                $item = new $className($sourceSite, $destinationSite);
                $item->setConnection($sourceSite)->copy();
                $item->setConnection($destinationSite)->paste();
                $done[$resource] = 1;
            }
        }

        // create entry for clone table
        $clone = new ModelsSiteClone();
        $clone->source_site_id = $sourceSite->id;
        $clone->destination_site_id = $destinationSite->id;
        $clone->cloned_at = date('Y-m-d H:i:s');
        $clone->resources = $inputs['resources'];
        $clone->save();


        $msg = 'Clone done for ' . implode(',', array_keys($done));
        Flash::success($msg);

        // clear cache and redirect to site dashboard
        NP::clearCache($destinationSite);
        SiteSessionData::setSite($destinationSite);
        $redirectUrl = config('cms.backendUri') . '/np/contents/contenttype';
        return redirect($redirectUrl);
    }
}
