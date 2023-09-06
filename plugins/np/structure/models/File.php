<?php

namespace Np\Structure\Models;

use System\Models\File as BaseFile;
use Illuminate\Support\Facades\URL;
use Backend\Facades\BackendAuth;
use Np\Structure\Classes\NP;

class File extends BaseFile
{

    use \October\Rain\Database\Traits\SoftDelete;


    protected $dates = ['deleted_at'];

    public function getUploadDirName()
    {
        return 'central';
    }

    public function isMigratedFile()
    {
        return substr_count($this->disk_name, '/');
    }

    public function getPath($fileName = null)
    {
        if ($this->isMigratedFile())
            return $this->getPublicPath() . rtrim(ltrim($this->disk_name, '/'), '/');

        return parent::getPath();
    }

    public function getPublicPath()
    {
        $uploadsPath = config('cms.storage.uploads.path');

        if ($this->isMigratedFile()) {
            $uploadsPath .= '/' . config('cms.storage.uploads.migrated_folder');
        } else {
            $uploadsPath .= '/' . config('cms.storage.uploads.folder') . '/' . $this->getUploadDirName() . '/';
        }

        return URL::asset($uploadsPath) . '/';
    }

    //Define the internal storage path.

    public function getStorageDirectory()
    {
        if ($this->isMigratedFile()) {
            return $uploadsFolder = config('cms.storage.uploads.migrated_folder');
        } else {
            $uploadsFolder = config('cms.storage.uploads.folder');
            return $uploadsFolder . '/' . $this->getUploadDirName() . '/';
        }
    }

    protected function getPartitionDirectory()
    {
        if ($this->isMigratedFile())
            return ''; //$this->disk_name . '/';

        return parent::getPartitionDirectory();
    }
}
