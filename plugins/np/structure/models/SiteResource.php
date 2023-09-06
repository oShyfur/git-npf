<?php

namespace Np\Structure\Models;

use Model;
use System\Models\MailPartial;

/**
 * Model
 */
class SiteResource extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;
    protected $jsonable = ['content_types', 'taxonomies', 'blocks', 'views', 'forms'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_structure_site_resources';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'site' => 'required'
    ];

    public $belongsTo = [
        'site' => ['Np\Structure\Models\Site']
    ];

    public function getContentTypesOptions()
    {
        $cts = ContentType::all()->pluck('name', 'id');
        return $cts;
    }

    public function getTaxonomiesOptions()
    {
        $taxonomy_types = TexonomyType::all()->pluck('name', 'id');
        return $taxonomy_types;
    }

    public function getBlocksOptions()
    {
        $blocks = Block::where('type', 1)->where('status', 1)->pluck('title', 'id');
        return $blocks;
    }

    public function getViewsOptions()
    {
        $blocks = Block::where('type', 2)->where('status', 1)->pluck('title', 'id');
        return $blocks;
    }

    public function getFormsOptions()
    {
        return MailPartial::where('code', 'like', 'form-%')->orderBy('name', 'asc')->pluck('name', 'id')->all();
    }
}
