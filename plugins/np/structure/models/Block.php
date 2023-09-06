<?php

namespace Np\Structure\Models;

use Illuminate\Support\Carbon;
use Model;
use System\Models\MailPartial;
use Illuminate\Support\Facades\Cache;

/**
 * Model
 */
class Block extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    use \Np\Structure\Traits\Cacheable;

    public $implement = [
        'RainLab.Translate.Behaviors.TranslatableModel',
    ];

    public $translatable = [
        'title',
        'body'
    ];

    //jsonable
    public $jsonable = ['layer_id'];


    protected $dates = ['deleted_at'];

    public $cacheTags = ['blocks'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_blocks';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required',
        'region' => 'required_if:type,==,1'
    ];

    public function getPredefinedTemplateCodeOptions()
    {
        $view_code = 'predefined-template-%';
        return MailPartial::where('code', 'like', $view_code)->orderBy('created_at', 'desc')->pluck('name', 'code')->all();
    }
    public function getPartialCodeOptions()
    {
        $view_code = '';
        $partials = [];
        if ($this->type == 1)
            $view_code = 'block-%';
        elseif ($this->type == 2)
            $view_code = 'list-%';

        if ($view_code)
            $partials = MailPartial::where('code', 'like', $view_code)->orderBy('created_at', 'desc')->pluck('name', 'code')->all();

        return $partials;
    }

    public function getType()
    {
        return $this->type == 1 ? 'block' : 'view';
    }

    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->whereDate('publish_date', '<=', Carbon::today())->orWhereNull('publish_date');
        });
    }
    public function scopeArchived($query)
    {
        return $query->where(function ($q) {
            $q->whereDate('archive_date', '>', Carbon::today())->orWhereNull('archive_date');
        });
    }
}
