<?php

namespace Np\Structure\Classes\Jobs;

use Np\Structure\Classes\NP;
use DB;
use Storage;

class CloneFiles extends BaseClone
{

    public $table = 'system_files';

    public function copy()
    {
        if ($this->connectionSite) {
            $rows = DB::connection('tenant')
            ->table($this->table)
            ->whereIn('attachment_id', array_keys($this->attachmentIds))
            ->get()
            ->toArray();
            
            foreach ($rows as $item) {
                // banner image copy to destination site
                $sourcePath = '/' . config('cms.storage.uploads.folder') . '/' . $this->sourceSite->uuid . '/' . implode('/', array_slice(str_split($item->disk_name, 3), 0, 3)) . '/' . $item->disk_name;
                $destinationPath = '/' . config('cms.storage.uploads.folder') . '/' . $this->destinationSite->uuid . '/' . implode('/', array_slice(str_split($item->disk_name, 3), 0, 3)) . '/' . $item->disk_name;
                $exists = Storage::disk(config('cms.storage.uploads.disk'))->exists($sourcePath);
                if($exists){
                    Storage::copy($sourcePath, $destinationPath);
                }

                if (isset($this->attachmentIds[$item->attachment_id])) {
                    $oldId = $item->id;
                    $this->oldIds[$oldId] = 0;
                    $item->attachment_id = $this->attachmentIds[$item->attachment_id];
                    $itemArray = (array) $item;
                    unset($itemArray['id']);
                    $this->items[] = $itemArray;
                }
            }
        }
        return $this;
    }


    public function paste()
    {
        // paste to destination site
        if ($this->connectionSite) {
            DB::connection('tenant')->table($this->table)->insert($this->items);
        }

        return $this;
    }

    public function delete($ids)
    {
        // paste to destination site
        if ($this->connectionSite) {
            DB::connection('tenant')->table($this->table)->whereIn('attachment_id', $ids)->delete();
        }

        return $this;
    }
}
