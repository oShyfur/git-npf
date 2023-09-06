<?php

namespace Np\Contents\Models;

use Model;
use Np\Structure\Models\NPBaseModel;
use System\Models\MailPartial;

/**
 * Model
 */
class SiteBlocks extends NPBaseModel
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    use \Np\Contents\Traits\Auditable;
    use \Np\Contents\Traits\SiteContentsTrait;

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = ['title', 'body'];


    public $connection = 'tenant';
    protected $dates = ['deleted_at'];
    public $fillable = ['title', 'code', 'region', 'sort_order'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_blocks';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'region' => 'required'
    ];


    public static function allRegion()
    {
        return [
            'top' => 'Top',
            'left' => 'Left',
            'right' => 'Right',
            'footer' => 'Footer',
            'no_region' => 'No Region'
        ];
    }
    public function getRegionOptions()
    {
        return self::allRegion();
    }
    public function getPredefinedTemplateCodeOptions()
    {
        $view_code = '';
        $partials = [];
        // if ($this->type == 1)
        //     $view_code = 'block-%';
        // elseif ($this->type == 2)
        //     $view_code = 'list-%';

        $view_code = 'predefined-%';
        if ($view_code)
            $partials = MailPartial::where('code', 'like', $view_code)->orderBy('created_at', 'desc')->pluck('name', 'code')->all();

        return $partials;
    }
    public function getType()
    {

        return $this->type == 2 ? 'site_block' : 'central_block';
    }
}
