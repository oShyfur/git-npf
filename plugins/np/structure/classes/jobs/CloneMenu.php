<?php

namespace Np\Structure\Classes\Jobs;

use Np\Structure\Classes\NP;
use DB;
use Exception ;
use Np\Structure\Classes\MyTranslate ;
use Log ;
class CloneMenu extends BaseClone
{

    public $table = 'np_contents_menus';


    public function copy()
    {

        if ($this->connectionSite) {

            $menu = DB::connection('tenant')->table($this->table)->where('site_id', $this->sourceSite->id)->get()->toArray();

            $newSiteId = $this->destinationSite->id;
            $ids = [];
            foreach ($menu as $item) {

                $this->oldIds[] = $item->id;

                if ($this->generateNewId) {
                    $oldId = $item->id;
                    $item->id = $newId = NP::generate_uuid();
                    $ids[$oldId] = [$newId, $item->parent_id];
                }

                $item->site_id = $newSiteId;
                $this->items[] = (array) $item;
            }

            if ($this->generateNewId)
                foreach ($this->items as &$item) {
                    if ($parentId = $item['parent_id']) {

                        if (isset($ids[$parentId]))
                            $item['parent_id'] = $ids[$parentId][0];
                    }
                }

            $this->copyLocalization($ids) ;
        }
        return $this;
    }


    public function copyLocalization($ids){

        try {
            // get old ids 
            $old_ids = array_keys($ids) ;
            // fetch data 
            $data = DB::connection('tenant')->table('rainlab_translate_attributes')->select(['locale','model_id','model_type','attribute_data'])
                                            ->whereIn('model_id',$old_ids )->get()->toArray() ;

            
            

            // Log::info($ids) ;
            // transform data 
            $t_data = [] ;
            foreach($data as $item) {
                $t = $item ;
                $t->model_id = $ids[ $t->model_id ][0] ; //set  new id 
                $t_data[] = (array) $t ;
            }
            

            // Log::info($t_data);

            // insert data 
            DB::connection('tenant')->table('rainlab_translate_attributes')->insert($t_data) ;
            //DB::connection('tenant')->table('rainlab_translate_attributes')->count()

        }catch(Exception $e) {
            Log::error($e);
            return ;
        }
    }

    public function paste()
    {
        // paste to destination site
        if ($this->connectionSite) {

            DB::connection('tenant')->table($this->table)->where('site_id', $this->destinationSite->id)->delete();
            DB::connection('tenant')->table($this->table)->insert($this->items);
        }

        return $this;
    }
}
