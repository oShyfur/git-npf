<?php

namespace Np\Contents\Models;

use Np\Structure\Models\NPBaseModel;
use October\Rain\Database\Builder;
use Np\Contents\Scopes\SiteScope;
use Np\Structure\Classes\NP;

/**
 * Model
 */
class NPContentsBaseModel extends NPBaseModel
{
    use \Np\Structure\Traits\UsesUuid;
    use \Np\Contents\Traits\Revisionable;
    use \Np\Contents\Traits\AttachmentDeletable;
    use \Np\Contents\Traits\Auditable;
    use \Np\Contents\Traits\SiteContentsTrait;
    use \Jacob\Logbook\Traits\LogChanges;
    
    // for updated_at content update last date
    // protected $touches = ['site'];

    public $connection = 'tenant';
    public $revisionableLimit = 2;

    public function getPreviewLink()
    {
        $modelParts = explode('\\', get_class($this));
        $model = end($modelParts);

        return 'http://' . NP::getPreviewDomain() . '/bn/site/' . NP::CamelCaseToSnakeCase($model) . '/' . $this->slug;
    }

    public function scopeWithoutSiteScope($query)
    {
        return $query->withoutGlobalScopes();
    }

    public function scopeWithCentralData($query, $centralSiteId = 13)
    {
        // $centralSiteId = 13;
        return $query->whereIn('site_id', [$centralSiteId, $this->getCurrentSite('id')])->withoutGlobalScopes();
    }


    public function getCurrentSite($key = null)
    {
        return NP::getSite($key);
    }
}
