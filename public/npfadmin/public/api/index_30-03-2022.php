<?php

class myApi{

    private $mainPath;
    private $envFilePath;
    private $db;
    private $db1;
    private $data;
    private $domain;
    private $lang;
    private $dbHost;
    private $dbName;
    private $dbUser;
    private $dbPassword;
    private $dbCharSet;

    public function __construct(){
        $this->dbCharSet = 'utf8';
        $this->mainPath = '/npfadmin/public/api/';
        $this->envFilePath = '../../../../.env';
        $this->dbConnect();
        $this->data = $_POST;
        $this->requiredParam();
        $this->preventInj();
        $this->domain = $this->data['domain'];
        $this->lang = $this->data['search_language'];
    }

    public function getApiName(){
        // return 'hello';
        return str_replace($this->mainPath,'',$_SERVER['REQUEST_URI']);
    }

    private function requiredParam(){
        if (empty($this->data["api_key"]) && $this->data["api_key"] != "abcd1234") {
            $result = array('status' => 'error', 'msg' => "Missing Api Key");
            $this->message($result);
        }
        /*if (empty($this->data["domain"])) {
            $result = array('status' => 'error', 'msg' => "Missing Search Key");
            $this->message($result);
        }*/
        if (empty($this->data["search_language"])) {
            $result = array('status' => 'error', 'msg' => "Missing Language Key");
            $this->message($result);
        }
    }

    private function preventInj(){
        foreach($this->data as $data) {
            $error = 0;
            if (strpos($data, "'") !== false)
                $error = 1;
            if (strpos($data, '"') !== false)
                $error = 1;
            if (strpos(strtolower($data), 'select') !== false)
                $error = 1;
            if (strpos(strtolower($data), 'delete') !== false)
                $error = 1;
            if (strpos(strtolower($data), 'update') !== false)
                $error = 1;
            if (strpos(strtolower($data), 'union') !== false)
                $error = 1;

            if ($error == 1) {
                $result = array('status' => 'error', 'msg' => 'Invalid data');
                $this->message($result);
            }
        }
    }

    private function dbConnect(){
        /*$env = file_get_contents($this->envFilePath);
        $env = preg_replace('/\s+/', ' ', trim($env));
        $env = explode(' ',$env);

        $tmp = array();
        foreach($env as $v){
            $t = explode('=',$v);
            $tmp[$t[0]] = str_replace('"','',str_replace("'","",$t[1]));
        }
        $env = $tmp;*/

        /*$this->dbHost='35.155.238.31';
        $this->dbName='np_backend';
        $this->dbUser='application';
        $this->dbPassword='q1w2e3r4';*/

        // $this->dbHost='127.0.0.1';
        // $this->dbName='np_backend';
        // $this->dbUser='root';
        // $this->dbPassword='';

        $this->dbHost = getenv('DB_HOST_BACKEND');
        $this->dbName = getenv('DB_DATABASE_BACKEND');
        $this->dbUser = getenv('DB_USERNAME_BACKEND');
        $this->dbPassword = getenv('DB_PASSWORD_BACKEND');

        try {
            $dsn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName . ';charset=' . $this->dbCharSet;
            $this->db = new \PDO($dsn, $this->dbUser, $this->dbPassword);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->db->exec("set names " . $this->dbCharSet);
        }catch (\Exception $ex){
            print_r($ex->getMessage());
            $result = array('status' => 'error', 'msg' => 'Database connection failed.');
            $this->message($result);
        }
    }

    private function sql($sql, $database = 'backend', $dbName=null){
        if($database=='frontend') {

			$this->dbHost = getenv('DB_HOST_TENENT');
			if($dbName!=null)
				$this->dbName = $dbName;
			else
				$this->dbName = getenv('DB_DATABASE_TENENT');
			//$this->dbUser = getenv('DB_USERNAME_TENENT');
			//$this->dbPassword = getenv('DB_PASSWORD_TENENT');
			
			//$dsn = 'mysql:host=' . '172.16.218.30' . ';dbname=' . $dbName . ';charset=' . $this->dbCharSet;
            //$dsn = 'mysql:host=' . getenv('DB_HOST_TENENT') . ';dbname=' . $dbName . ';charset=' . $this->dbCharSet;
			$dsn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName . ';charset=' . $this->dbCharSet;
            $this->db1 = new \PDO($dsn, $this->dbUser, $this->dbPassword);
            $this->db1->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->db1->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
            $this->db1->exec("set names " . $this->dbCharSet);

            $result = $this->db1->prepare($sql);
        }else $result = $this->db->prepare($sql);

        $result->execute();
        $row = $result->fetchAll(\PDO::FETCH_OBJ);
        if(count($row)==1)
            $row = $row[0];
        return $row;
    }

    private function message($output){
        header('Content-Type: application/json');
        echo json_encode($output, JSON_UNESCAPED_UNICODE);
        die();
    }

    private function getApiDomainType($id)
    {
        $domain_type = [4 => 'division', 5 => 'district', 6 => 'upazilla', 7 => 'union', 14 => 'office'];
        return $domain_type[$id];
    }

    public function getOfficeListv2(){
        try {
            $sql = 'select * from np_structure_domains where fqdn="' . $this->domain . '"';
            $site = $this->sql($sql);

            $sql = 'select * from np_structure_sites where id="' . $site->site_id . '"';
            $domain = $this->sql($sql);

            $sql = 'select * from np_structure_sites where id="' . $domain->parent_id . '"';
            $pardom = $this->sql($sql);

            $sql = 'select * from np_structure_domains where site_id="' . $pardom->id . '"';
            $pardomain = $this->sql($sql);

            $col = "sitename_" . $this->lang;

            $head_title = "";
            if ($domain->layer_id == 4) {
                $head_title = "বিভাগীয় কমিশনারের কার্যালয়";
            } else if ($domain->layer_id == 5) {
                $head_title = "জেলা প্রশাসকের কার্যালয়";
            } else if ($domain->layer_id == 6) {
                $head_title = "উপজেলা নির্বাহী অফিসারের কার্যালয়";
            } else if ($domain->layer_id == 7) {
                $head_title = "ইউনিয়ন পরিষদ";
            }
            $data[$site->fqdn] = [
                'id' => $domain->id,
                'name' => $head_title, 'subdomain' => $site->fqdn, 'parent_id' => $domain->parent_id, 'parent_domain' => $pardomain->fqdn, 'parent_name' => $pardom->name, 'parent_domain_type' => $this->getApiDomainType($pardom->layer_id), 'domain_type' => $this->getApiDomainType($domain->layer_id)
            ];

            if (!empty($domain)) {
                //$sql = 'Select * from np_structure_sites where parent_id="' . $domain->id . '" and layer_id="'.$domain->layer_id.'" and active=1 order by sort_order';
				$sql = 'Select * from np_structure_sites where parent_id="' . $domain->id . '"  AND id NOT IN (7234,9492,22464,30773,50110) order by sort_order';
                $npfOffices = $this->sql($sql);
				//print_r($npfOffices);
                foreach ($npfOffices as $nofc) {
                    $col = "sitename_" . $this->lang;
					
					$sql = 'select * from rainlab_translate_attributes where model_id="' . $nofc->id . '" and model_type="Np\\\\Structure\\\\Models\\\\Site" limit 1';
                    $nofc1 = $this->sql($sql);
					
					$nofc1 = json_decode($nofc1->attribute_data,true);
					//print_r($nofc1);
					if(isset($nofc1['name']))
						$nofc->name = $nofc1['name'];

                    $sql = 'select * from np_structure_domains where site_id="' . $nofc->id . '" limit 1';
                    $pardomain = $this->sql($sql);

                    $data[$pardomain->fqdn] = [
                        'id' => $nofc->id,
                        'name' => $nofc->name, 'subdomain' => $pardomain->fqdn, 'parent_id' => $domain->id, 'parent_domain' => $site->fqdn, 'parent_name' => $domain->name, 'parent_domain_type' => $this->getApiDomainType($pardom->layer_id), 'domain_type' => $this->getApiDomainType($nofc->layer_id)
                    ];
                }
                $result = ['status' => 'success', 'data' => $data];
            }

            $temp = $result;
            unset($temp['status']);
            $result = array();

            $i = 0;
            foreach ($temp as $key => $value) {

                foreach ($value as $v) {
                    $result['data']['sites'][$i] = $v;
                    $result['data']['sites'][$i]['name'] = array('bn' => $v['name'], 'en' => $v['name']);
                    $result['data']['sites'][$i]['domain'] = $result['data']['sites'][$i]['subdomain'];
                    $i = $i + 1;
                }
            }
            $this->message($result);
        }catch (\Exception $ex){
            print_r($ex->getMessage());
        }
    }

    public function getOfficeEmployees(){

        try {
            $officer_list = [];
            $key = $this->data['domain'];
            $key_bn = $this->data['search_language'];
            $domain_type = $this->data['domain_type'];

            //$sql = 'Select * from npf_domains where subdomain="' . $key . '"';
            //$domain = $this->sql($sql);

            $sql = 'Select * from np_structure_domains where fqdn="' . $key . '" limit 1';
            $sites = $this->sql($sql);

            $sql = 'Select uuid, db_id from np_structure_sites where id="' . $sites->site_id . '" limit 1';
            $database = $this->sql($sql);
			
            if (!empty($sites) && $sites!="") {
                $uploaddomain = $sites->fqdn;
                $uploadpath = $database->uuid;
                switch ($database->db_id) {
                    case '01f524cd-7b01-42ef-a4a2-d9c187ed0401':
                        $cdn_path = "file-barisal.portal.gov.bd";
                        break;
                    case '62cdc646-a6cc-45f3-8713-9799b36c396a':
                        $cdn_path = "file-chittagong.portal.gov.bd";
                        break;
                    case '9bdf8720-f234-4121-b3f6-795948ad9e33':
                        $cdn_path = "file-dhaka.portal.gov.bd";
                        break;
                    case '6a1ce5d7-c137-4fdc-9fb8-85c0fbd2ddfa':
                        $cdn_path = "file-mymensingh.portal.gov.bd";
                        break;
                    case 'a0e29d09-395c-49d5-9d4e-d23ba3e63943':
                        $cdn_path = "file-rajshahi.portal.gov.bd";
                        break;
                    case '5b8641e5-db8e-4372-a165-bec7b16d28c8':
                        $cdn_path = "file.portal.gov.bd";
                        break;
                    default:
                        echo "No CDN";
                        break;
                }

                 $sql = "SELECT a.id, `title` as title, `designation` as designation_old, `designation` as current_designation
                    FROM np_contents_officer_list a
                    WHERE publish = 1 AND deleted_at is null AND site_id=" . $sites->site_id . " order by field_batch, id_number";
					//`{$database->db_id}`.
                if ($domain_type == 'upazilla' || $domain_type == 'upazila') {
                    $domain = str_replace("www.", "", $key);
                    $domain_array = explode(".", $domain);
                    if (count($domain_array) == 4) {
                        $sql = "SELECT a.id, `title` as title, `designation` as designation_old, `designation` as current_designation
                        FROM np_contents_officer_list a
                        WHERE publish = 1 AND deleted_at is null AND site_id=" . $sites->site_id . " order by b.created desc, field_batch , id_number LIMIT 1";
						//`{$database->db_id}`.
                    }
                }

                $result = $this->sql($sql,'frontend',$database->db_id);
                $officer_list = array();

                foreach ($result as $row) {
                    $ofcr = get_object_vars($row);

                    $sql = 'Select * from system_files where attachment_id="' . $ofcr['id'] . '" and attachment_type="Np\\\\Contents\\\\Models\\\\OfficerList" ORDER BY id DESC LIMIT 1';
					//`' . $database->db_id . '`.
                    $photo = $this->sql($sql,'frontend',$database->db_id);

                    if ((isset($photo->disk_name)) && (strpos($photo->disk_name,'officer_list') !== false)) {
                        // phalcon
                        $disk_name = $photo->disk_name;
						$ltrim_photo = ltrim($disk_name,'/');
						$u = explode('/',$ltrim_photo);

                        $ofcr['uploaddomain'] = $uploaddomain;
						// if(isset($u[0])){
						// 	$ofcr['uploaddomain'] = $u[0];
                        // } else {
                        //     $ofcr['uploaddomain'] = '';
                        // }

						if(isset($u[2])) {
							$ofcr['uploadpath'] = str_replace('_','-',$u[2]);
                        } else {
                            $ofcr['uploadpath'] = '';
                        }
                        
                        $ofcr['photo'] = $cdn_path."/files/".$ltrim_photo;

                    } else {
                        // october
                        $sub_path = substr($photo->disk_name, 0, 3).'/'.substr($photo->disk_name, 3, 3).'/'.substr($photo->disk_name, 6, 3);
                        $ofcr['photo'] = $cdn_path."/uploads/".$uploadpath."/".$sub_path."/".$photo->disk_name;
						$ofcr['uploaddomain'] = $uploaddomain;
						$ofcr['uploadpath'] = $uploadpath;
                    }                  
					$ofcr['organization'] = '';
					
					if(!isset($ofcr['title'])) $ofcr['title'] = '';
					if(!isset($ofcr['designation_old'])) $ofcr['designation_old'] = '';
					if(!isset($ofcr['current_designation'])) $ofcr['current_designation'] = '';

                    $officer_list[] = $ofcr;
                }
            }
            $result = ['status' => 'success', 'data' => $officer_list];

            echo json_encode($result);
            die();
        }catch (\Exception $ex){
            print_r($ex->getMessage());
        }
    }

    public function getOfficer()
    {
        try {
            $key = $this->data['domain'];
            $key_bn = $this->data['search_language'];

            $sql = 'Select * from np_structure_domains where fqdn="' . $key . '"';
            $sites = $this->sql($sql);

            $sql = 'Select name,uuid, db_id from np_structure_sites where id="' . $sites->site_id . '"';
            $database = $this->sql($sql);

            $sql = $sql = 'Select * from rainlab_translate_attributes where model_id="' . $sites->site_id . '" and model_type="Np\\\\Structure\\\\Models\\\\Site"';
            $translate = $this->sql($sql);

            $uploaddomain = $sites->fqdn;
            $uploadpath = $database->uuid;
            switch ($database->db_id) {
                case '01f524cd-7b01-42ef-a4a2-d9c187ed0401':
                    $cdn_path = "file-barisal.portal.gov.bd";
                    break;
                case '62cdc646-a6cc-45f3-8713-9799b36c396a':
                    $cdn_path = "file-chittagong.portal.gov.bd";
                    break;
                case '9bdf8720-f234-4121-b3f6-795948ad9e33':
                    $cdn_path = "file-dhaka.portal.gov.bd";
                    break;
                case '6a1ce5d7-c137-4fdc-9fb8-85c0fbd2ddfa':
                    $cdn_path = "file-mymensingh.portal.gov.bd";
                    break;
                case 'a0e29d09-395c-49d5-9d4e-d23ba3e63943':
                    $cdn_path = "file-rajshahi.portal.gov.bd";
                    break;
                case '5b8641e5-db8e-4372-a165-bec7b16d28c8':
                    $cdn_path = "file.portal.gov.bd";
                    break;
                default:
                    echo "No CDN";
                    break;
            }

            $sql = "SELECT * FROM np_contents_officer_list WHERE publish = 1 AND id='" . $this->data['officer_id'] . "'";
			//`" . $database->db_id . "`.

            $ofcr = $this->sql($sql,'frontend',$database->db_id);
            $ofcr = (array)$ofcr;

            $sql = 'Select * from system_files where attachment_id="' . $ofcr['id'] . '" and attachment_type="Np\\\\Contents\\\\Models\\\\OfficerList" ORDER BY id DESC LIMIT 1';
			//`' . $database->db_id . '`.
            $photo = $this->sql($sql,'frontend',$database->db_id);

            // if (isset($photo->disk_name))
            //     $ofcr['photo'] = $photo->disk_name;
            // else $ofcr['photo'] = "";
			
			// $ofcr['photo'] = ltrim($ofcr['photo'],'/');
			// $u = explode('/',$ofcr['photo']);

			// if(isset($u[0]))
			// 	$ofcr['uploaddomain'] = $u[0];
			// else $ofcr['uploaddomain'] = '';
			
			// if(isset($u[2]))
			// 	$ofcr['uploadpath'] = str_replace('_','-',$u[2]);
			// else $ofcr['uploadpath'] = '';

            if ((isset($photo->disk_name)) && (strpos($photo->disk_name,'officer_list') !== false)) {
                // phalcon
                $disk_name = $photo->disk_name;
                $ltrim_photo = ltrim($disk_name,'/');
                $u = explode('/',$ltrim_photo);

                $ofcr['uploaddomain'] = $uploaddomain;
                // if(isset($u[0])){
                // 	$ofcr['uploaddomain'] = $u[0];
                // } else {
                //     $ofcr['uploaddomain'] = '';
                // }

                if(isset($u[2])) {
                    $ofcr['uploadpath'] = str_replace('_','-',$u[2]);
                } else {
                    $ofcr['uploadpath'] = '';
                }
                
                $ofcr['photo'] = $cdn_path."/files/".$ltrim_photo;

            } else {
                // october
                $sub_path = substr($photo->disk_name, 0, 3).'/'.substr($photo->disk_name, 3, 3).'/'.substr($photo->disk_name, 6, 3);
                $ofcr['photo'] = $cdn_path."/uploads/".$uploadpath."/".$sub_path."/".$photo->disk_name;
                $ofcr['uploaddomain'] = $uploaddomain;
                $ofcr['uploadpath'] = $uploadpath;
            }


            $attribute_data = json_decode($translate->attribute_data);
			$ofcr['org_name_bn']=$attribute_data->name;
			$ofcr['org_name_en']=$database->name;

            $officer_list = $ofcr;

            $result = ['status' => 'success', 'data' => $officer_list];

            echo json_encode($result);
            die();
        }catch (\Exception $ex){
            print_r($ex->getMessage());
        }
    }
}

$api = new myApi();

$apiName = $api->getApiName();

if($apiName=='getOfficeListv2')
    $api->getOfficeListv2();
else if($apiName=='getOfficeEmployees')
    $api->getOfficeEmployees();
else if($apiName=='getOfficer')
    $api->getOfficer();