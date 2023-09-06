<?php

namespace Np\Structure\Classes\Jobs;

use Np\Structure\Classes\NP;
use DB;

class CloneServiceBox extends BaseClone
{

    public $table = 'np_contents_front_service_box';

    public function copy()
    {

        if ($this->connectionSite) {

            $rows = DB::connection('tenant')->table($this->table)->where('site_id', $this->sourceSite->id)->get()->toArray();

            $newSiteId = $this->destinationSite->id;
            $newId = 0;
            foreach ($rows as $item) {

                $oldId = $item->id;
                if ($this->generateNewId) {
                    $item->id = $newId = NP::generate_uuid();
                }
                $this->oldIds[$oldId] = $newId;

                $item->site_id = $newSiteId;
                $this->items[] = (array) $item;
            }
        }
    }



    public function paste()
    {
        // paste to destination site
        if ($this->connectionSite) {

            $oldRows = DB::connection('tenant')->table($this->table)->where('site_id', $this->destinationSite->id);

            //delete files from system file
            $this->deletedIds = $oldRows->get()->pluck('id');
            DB::connection('tenant')->table('system_files')->whereIn('attachment_id', $this->deletedIds)->delete();

            //delete old banners
            $oldRows->delete();
            DB::connection('tenant')->table($this->table)->insert($this->items);

            //copy banner images
            $this->cloneFiles();
        }
    }
}
