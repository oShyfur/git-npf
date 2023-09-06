<?php namespace Np\Contents\Traits;

use Illuminate\Support\Facades\Log;

trait AttachmentDeletable
{


    public static function bootAttachmentDeletable()
    {

        static::extend(function ($model) {
            $model->bindEvent('model.afterDelete', function () use ($model) {
                $model->deleteAttachments();
            });
        });
    }

    public function deleteAttachments()
    {

        $attachMany = property_exists($this, 'attachMany') ? $this->attachMany : null;
        if ($attachMany) {
            foreach ($attachMany as $k => $v) {
                foreach ($this->{$k} as $file) {
                    $file->delete();
                }
            }
        }

        $attachOne = property_exists($this, 'attachOne') ? $this->attachOne : null;
        if ($attachOne) {
            foreach ($attachOne as $k => $v) {
                is_object($this->{$k}) ? $this->{$k}->delete() : '';
            }
        }
    }
}
