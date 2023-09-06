<?php

namespace Np\Contents\Classes;

use Lang;
use RainLab\Builder\Classes\ControlLibrary;
use Np\Structure\Models\TexonomyType;

class CustomControls
{

    public function registerControls($controlLibrary)
    {

        $this->registerLinkPicker($controlLibrary);
        $this->registertaxonomyPicker($controlLibrary);
    }
    /**
     * Handle user login events.
     */


    public function registerLinkPicker($controlLibrary)
    {

        $controlLibrary->registerControl(
            'linkpicker',
            'Link Picker',
            'Find Link ',
            ControlLibrary::GROUP_WIDGETS,
            'icon-picture-o',
            $controlLibrary->getStandardProperties(['stretch']),
            'Np\Contents\FormWidgets\ControlDesignTimeProvider'
        );
    }


    public function registertaxonomyPicker($controlLibrary)
    {
        $properties = [
            'taxonomyType' =>  [
                'title' => 'Select taxonomy Type',
                'type' => 'dropdown',
                'options' => $this->getTaxonomyTypes(),
                'sortOrder' => 81,
                'validation' => [
                    'required' => [
                        'message' => 'Select a Taxonomy Type'
                    ]
                ],
            ],
            'useSiteScope' =>  [
                'title' => 'Use Site Scope',
                'type' => 'checkbox',
                'sortOrder' => 82,
            ]
        ];

        $controlLibrary->registerControl(
            'taxonomypicker',
            'Taxonomy Picker',
            'Select Taxonomy Type',
            ControlLibrary::GROUP_WIDGETS,
            'icon-file-image-o',
            $controlLibrary->getStandardProperties(['stretch'], $properties),
            'Np\Contents\FormWidgets\ControlDesignTimeProvider'
        );
    }

    public function getTaxonomyTypes()
    {
        return TexonomyType::pluck('name', 'id')->all();
    }

    public static function addMaxFileSizetoUpload(&$properties)
    {
        //add file size

        $properties['maxFilesize'] =  [
            'title' => 'FileSize',
            'description' => 'file size in MB',
            'group' => Lang::get('rainlab.builder::lang.form.property_group_fileupload'),
            'type' => 'string',
            'ignoreIfEmpty' => true,
            'sortOrder' => 82
        ];
    }
}
