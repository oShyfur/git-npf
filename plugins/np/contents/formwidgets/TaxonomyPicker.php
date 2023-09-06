<?php

namespace Np\Contents\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Np\Structure\Models\TexonomyType;
use Np\Contents\Models\Taxonomy;
use Np\Contents\Scopes\SiteScope;
use Np\Structure\Classes\NP;

/**
 * TaxonomyPicker Form Widget
 */
class TaxonomyPicker extends FormWidgetBase
{

    public $taxonomyType;
    public $useSiteScope;
    public static $multiple = false;

    /**
     * @inheritDoc
     */
    protected $defaultAlias = 'taxonomypicker';

    /**
     * @inheritDoc
     */
    public function init()
    {
        $this->fillFromConfig([
            'taxonomyType',
            'useSiteScope'
        ]);
    }

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('taxonomypicker');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {

        $this->vars['id'] = $this->getId();
        $this->vars['name'] = $this->formField->getName();
        $this->vars['selectedValue'] = $this->getLoadValue();
        $this->vars['taxonomy'] = $this->getTaxonomies();
        $this->vars['taxonomyType'] = $this->taxonomyType;
        $this->vars['useSiteScope'] = $this->useSiteScope;
        $this->vars['multiple'] =    self::$multiple;
        //dd($this->vars);
    }

    public static function setMultipleOption($bool)
    {
        self::$multiple = $bool;
    }

    /**
     * @inheritDoc
     */
    public function loadAssets()
    {
        //$this->addCss('css/taxonomypicker.css', 'Np.Contents');
        //$this->addJs('js/taxonomypicker.js', 'Np.Contents');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        return $value;
    }

    public function getTaxonomies()
    {
        //$query = Taxonomy::withoutGlobalScope(SiteScope::class)->where('texonomy_type_id', $this->taxonomyType)->pluck('name', 'id')->all()

        $query = Taxonomy::where('texonomy_type_id', $this->taxonomyType);

        if ($this->useSiteScope)
            $query->where('site_id', NP::getSiteId());

        return $query->pluck('name', 'id')->all();
    }
}
