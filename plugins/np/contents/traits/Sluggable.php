<?php namespace Np\Contents\Traits;

use October\Rain\Support\Str;
use Exception;

trait Sluggable
{

    /**
     * @var array List of attributes to automatically generate unique URL names (slugs) for.
     *
     * protected $slugs = [];
     */

    /**
     * @var bool Allow trashed slugs to be counted in the slug generation.
     *
     * protected $allowTrashedSlugs = false;
     */

    /**
     * Boot the sluggable trait for a model.
     * @return void
     */
    public static function bootSluggable()
    {
        if (!property_exists(get_called_class(), 'slugs')) {
            throw new Exception(sprintf(
                'You must define a $slugs property in %s to use the Sluggable trait.',
                get_called_class()
            ));
        }

        /*
         * Set slugged attributes on new records and existing records if slug is missing.
         */
        static::extend(function ($model) {
            $model->bindEvent('model.saveInternal', function () use ($model) {
                $model->slugAttributes();
            });
        });
    }

    public function slugAttributes()
    {
        foreach ($this->slugs as $slugAttribute => $sourceAttributes) {
            $this->makeUniqueSlug($slugAttribute, $sourceAttributes);
        }
    }


    /**
     * Adds slug attributes to the dataset, used before saving.
     * @return void
     */
    public function makeUniqueSlug($slugAttribute, $sourceAttributes)
    {

        $slug = '';
        if (!is_array($sourceAttributes)) {
            $sourceAttributes = [$sourceAttributes];
        }
        foreach ($sourceAttributes as $attribute) {
            $slug .= $this->makeSlug($this->getSluggableSourceAttributeValue($attribute));
        }


        //$count = $this->newSluggableQuery()->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();
        $count = $this->newSluggableQuery()->whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->withTrashed()->count();
        $slug = $count ? "{$slug}-{$count}" : $slug;
        return $this->{$slugAttribute} = $slug;
    }
    public function makeSlug($string)
    {
        $string = trim($string, ",");
        if($string!="" && is_string($string)){
            //$string = str_replace(array('&','/','\\', "\0", "'", '"', "\x1a"), array('-','-','', '', "", '', ''), $string);
            $searchCharacter = array('%','$','#','(',')','{','}','[',']','&','/','\\', "\0", "'", '"', "\x1a");
            $replaceCharacter = array('-parentage','-dollar','-hash','-','-','-','-','-','-','-and','-','', '', "", '', '');
            $string = str_replace($searchCharacter, $replaceCharacter, $string);
        }
        return preg_replace('/\s+/u', $this->getSluggableSeparator(), trim($string));
    }


    /**
     * Returns a query that excludes the current record if it exists
     * @return Builder
     */
    protected function newSluggableQuery()
    {
        return $this->exists
            ? $this->newQuery()->where($this->getKeyName(), '<>', $this->getKey())
            : $this->newQuery();
    }

    /**
     * Get an attribute relation value using dotted notation.
     * Eg: author.name
     * @return mixed
     */
    protected function getSluggableSourceAttributeValue($key)
    {
        if (strpos($key, '.') === false) {
            return $this->getAttribute($key);
        }

        $keyParts = explode('.', $key);
        $value = $this;
        foreach ($keyParts as $part) {
            if (!isset($value[$part])) {
                return null;
            }

            $value = $value[$part];
        }

        return $value;
    }

    /**
     * Override the default slug separator.
     * @return string
     */
    public function getSluggableSeparator()
    {
        return defined('static::SLUG_SEPARATOR') ? static::SLUG_SEPARATOR : '-';
    }
}
