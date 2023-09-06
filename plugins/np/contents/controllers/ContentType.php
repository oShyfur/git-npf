<?php

namespace Np\Contents\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Np\Structure\Models\Site;

class ContentType extends Controller
{

    public function __construct()
    {
        // dd(session('site.resources.content_types'));
        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents-ct');
    }
    public function index()
    {
        // dd(session('site.resources'));
        // dd(session('site.resources.content_types'));
        $this->pageTitle = 'Content Types';
        $this->vars['list'] = session('site.resources.content_types'); 
        //Site::find(session('site.id'))->getSiteResources('content-type', session('site.resources.content_types'));
        $this->vars['contentLastUpdate'] = session('site.resources.contentLastUpdate'); 
    }
}
