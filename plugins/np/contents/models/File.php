<?php

namespace Np\Contents\Models;

use Exception;
use File as FileHelper;
use Np\Structure\Classes\NP;
use Backend\Facades\BackendAuth;
use Illuminate\Support\Facades\URL;
use System\Models\File as BaseFile;
use October\Rain\Database\Attach\Resizer;
use October\Rain\Database\Attach\BrokenImage;

class File extends BaseFile
{

    use \October\Rain\Database\Traits\SoftDelete;

    public $connection = 'tenant';

    protected $dates = ['deleted_at'];

    protected $guarded=[];

    public function getUploadDirName()
    {
        return NP::getSite('uuid') ? NP::getSite('uuid') : 'central';
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
    public function getCroppedFilename()
    {
        $diskNames = explode('.', $this->disk_name);
        $dn = $diskNames[0];
        $ext = end($diskNames);

        return $dn . '1' . '.' . $ext;
    }

    public function cropImage($data)
    {
        //resize image
        $x = (int)$data['x'];
        $y = (int)$data['y'];
        $width = (int)$data['width'];
        $height = (int)$data['height'];
        
        // $img = $this->disk_name;
        // $filePath = $this->getStorageDirectory() . $this->getPartitionDirectory();
        // $newFileName = $this->getCroppedFilename();
        // logger($filePath);
        // Resizer::open($filePath . $img)
        //     ->crop($x, $y, $width, $height)
        //     ->save($filePath . $newFileName);
        
        // $this->disk_name = $newFileName;
        // $this->save();
        
        // return $this;
        
        $cropFile = $this->getCroppedFilename();
        $cropPath = $this->getStorageDirectory() . $this->getPartitionDirectory() . $cropFile;
        $this->makeCropStorage($cropFile, $cropPath, $width, $height, $x, $y);
        
        //save new file name
        $this->disk_name = $cropFile;
        $this->save();

        return $this;
    }

    public function nothiFile($data)
    {
        $data->save();
    }

    public function makeCropStorage($thumbFile, $thumbPath, $width, $height, $x, $y)
    {

        $tempFile = $this->getLocalTempPath();
        $tempThumb = $this->getLocalTempPath($thumbFile);

        /*
         * Handle a broken source image
         */
        if (!$this->hasFile($this->disk_name)) {
            BrokenImage::copyTo($tempThumb);
        }
        /*
         * Generate thumbnail
         */ else {
            $this->copyStorageToLocal($this->getDiskPath(), $tempFile);

            try {
                Resizer::open($tempFile)
                    ->crop($x, $y, $width, $height)
                    ->save($tempThumb);
            } catch (Exception $ex) {
                BrokenImage::copyTo($tempThumb);
            }

            FileHelper::delete($tempFile);
        }

        /*
         * Publish to storage and clean up
         */
        $this->copyLocalToStorage($tempThumb, $thumbPath);
        FileHelper::delete($tempThumb);
    }
}
