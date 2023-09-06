<?php 

namespace Np\Contents\Models;
// Update Start
use Np\Structure\Classes\NP;
use Np\Structure\Models\NPBaseModel;
// Update End
use October\Rain\Database\Models\Revision as RevisionBase;

/**
 * Revision history model
 *
 * @package october\system
 * @author Alexey Bobkov, Samuel Georges
 */
// class ContentRevision extends NPContentsBaseModel
// {
//     /**
//      * @var string The database table used by the model.
//      */
//     public $table = 'content_revisions';
//     protected $jsonable = ['fields'];
//     public $fillable = ['fields'];
// }

class ContentRevision extends NPBaseModel
{
    use \Np\Structure\Traits\UsesUuid;
    use \Np\Contents\Traits\SiteContentsTrait;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'content_revisions';
    protected $jsonable = ['fields'];
    public $fillable = ['fields'];

    public function beforeCreate()
    {
        $this->created_by = NP::getUserId();
        $this->updated_by = NP::getUserId();
    }

    public function beforeDelete()
    {
        $this->deleted_by = NP::getUserId();
    }
}


