<?php

namespace Np\Contents\Models;

use Illuminate\Support\Facades\DB;
use Model;
use Np\Structure\Models\NPBaseModel;
use Np\Contents\Scopes\SiteScope;
use Np\Structure\Classes\NP;

/**
 * Model
 */
// class Menu extends NPBaseModel
class Menu extends NPContentsBaseModel
{
    public $connection = 'tenant';

    use \Np\Structure\Traits\UsesUuid;
    use \Np\Contents\Traits\Auditable;
    use \Np\Contents\Traits\SiteContentsTrait;
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    use \October\Rain\Database\Traits\Sortable;
    use \October\Rain\Database\Traits\SimpleTree;


    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = ['title'];

    protected $dates = ['deleted_at'];

    const SORT_ORDER = 'sort_order';
    
    public $fillable = ['title', 'parent', 'link_type', 'link_path','menu_text_color','menu_background_color','default_text_color','default_background_color','sort_order','status'];

    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_menus';

    /**
     * @var array Validation rules
     */
    public $rules = [];

    /**
     * @var array - id, parent id casting
     */
    protected $casts = [
        'id' => 'string',
        'parent_id' => 'string'
    ];


    public function scopeOnlyParent($query)
    {
        return $query->whereIn('depth', [1, 2, 3]);
    }

    public function afterSave()
    {
        $defaultLang = NP::getSite('default_lang');
        //save translation
        if (post("RLTranslate")) {
            foreach (post("RLTranslate") as $key => $value) {

                if ($defaultLang == $key)
                    continue;

                $data = json_encode($value);

                $obj = DB::connection($this->connection)->table("rainlab_translate_attributes")
                    ->select(['id','locale','model_id','model_type','attribute_data'])
                    ->where("locale", $key)
                    ->where("model_id", $this->id)
                    ->where("model_type", get_class($this));

                if ($obj->count() > 0) {
                    $obj->update(['attribute_data' => $data]);
                } else {
                    DB::connection($this->connection)->table('rainlab_translate_attributes')->insert([
                        'locale' => $key,
                        'model_id' => $this->id,
                        'model_type' => get_class($this),
                        'attribute_data' => $data
                    ]);
                }
            }
        }
    }
}
