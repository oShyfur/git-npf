<?php

namespace Np\Api\Controllers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Twilio\Http\Response;
use Illuminate\Support\Facades\DB;

class NothiAPIIntegrationsController extends APIBaseController
{
    public function CreateContent(Request $request)
    {
        $data = $request->all();
        $domain_name = $data['publish_domain'];
        // Get site_id from 'np_structure_domains'
        $domain_id = DB::table('np_structure_domains')->where('fqdn',$domain_name)->first();
        //Get site from 'np_structure_sites'
        $site_id = DB:: table('np_structure_sites')->where('id',$domain_id->site_id)->first();
        // $tenent_db = DB::table($site_id->db_id)->where('');
        return response()->json(['success'=>'success','data'=>$site_id]);
    }

}