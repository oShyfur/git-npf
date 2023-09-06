<?php

namespace Np\Contents\Models;

use Np\Structure\Models\NPBaseModel;

/**
 * Model
 */
class Taxonomy extends NPBaseModel
{

    use \Np\Contents\Traits\Auditable;
    use \Np\Contents\Traits\SiteContentsTraitWithoutScope;
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    public $connection = 'tenant';

    public $implement = ['RainLab.Translate.Behaviors.TranslatableModel'];
    public $translatable = [
        'name'
    ];

    protected $dates = ['deleted_at'];


    /**
     * @var string The database table used by the model.
     */
    public $table = 'np_contents_texonomy';

    /**
     * @var array Validation rules
     */
    public $rules = [];

    public $belongsTo = [
        'texonomy_type' => 'Np\Contents\Models\TaxonomyType'
    ];

    public $hasMany = [

        'educational_institutes' => [
            'Np\Contents\Models\EducationInstitute',
            'key' => 'institute_type'
        ],
        'educational_institutes_count' => [
            'Np\Contents\Models\EducationInstitute',
            'key' => 'institute_type',
            'count' => true
        ],

        'religious_institutes' => [
            'Np\Contents\Models\ReligiousInstitutes',
            'key' => 'religious_institutes'
        ],
        'religious_institutes_count' => [
            'Np\Contents\Models\ReligiousInstitutes',
            'key' => 'religious_institutes',
            'count' => true
        ],

        'finalcial_institutes' => [
            'Np\Contents\Models\BankFinancialOrg',
            'key' => 'category_of_bank_financial_org'
        ],
        'finalcial_institutes_count' => [
            'Np\Contents\Models\BankFinancialOrg',
            'key' => 'category_of_bank_financial_org',
            'count' => true
        ]

    ];


    //custom functions

   public static function getInstitutesWithCount()
    {
        $relation = 'educational_institutes_count';
        $taxonomyName = 'Institute Type';

        return self::getContentsWithCount($relation, $taxonomyName);
    }


    public static function getReligiousInstitutesWithCount()
    {
        $relation = 'religious_institutes_count';
        $taxonomyName = 'ধর্মীয় প্রতিষ্ঠান';

        return self::getContentsWithCount($relation, $taxonomyName);
    }

    public static function getFinancialInstitutesWithCount()
    {
        $relation = 'finalcial_institutes_count';
        $taxonomyName = 'ব্যাংক ও আর্থিক প্রতিষ্ঠানের ধরণ';

        return self::getContentsWithCount($relation, $taxonomyName);
    }


    public static function getContentsWithCount($relation, $taxonomyName)
    {
        $list = Taxonomy::with($relation)
            ->where('status', 1)
            ->whereHas('texonomy_type', function ($q) use ($taxonomyName) {
                $q->where('name', $taxonomyName);
            })
            ->orderBy('id','asc')
            ->get()
            ->toArray();

        return $list;
    }
}
