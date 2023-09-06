<?php

namespace Np\Structure\Classes;

use Backend;
use Flash;
use Redirect;
use System\Controllers\MailPartials;
use System\Controllers\MailTemplates;
use System\Models\MailLayout;
use System\Models\MailPartial;
use System\Models\MailTemplate;

/**
 * Class BackendUserExtension
 * @package Renatio\Logout\Classes
 */
class SystemTemplateExtension
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->extendPartial();
        $this->extendTemplate();
        $this->enableCachingToModels();
    }

    public function enableCachingToModels()
    {
        MailLayout::extend(function ($model) {
            $model->implement[] = 'Np.Structure.Behaviors.CacheableModel';
        });

        MailTemplate::extend(function ($model) {
            $model->implement[] = 'Np.Structure.Behaviors.CacheableModel';
        });

        MailPartial::extend(function ($model) {
            $model->implement[] = 'Np.Structure.Behaviors.CacheableModel';
        });
    }

    /**
     * @return void
     */

    public function extendTemplate()
    {

        MailTemplates::extend(function ($controller) {
            $controller->formConfig = '';
            $myFormConfigPath = '$/np/structure/controllers/mailtemplates/config_form.yaml';
            $controller->formConfig = $controller->mergeConfig(
                $controller->formConfig,
                $myFormConfigPath
            );
        });
    }

    public function extendPartial()
    {

        MailPartials::extend(function ($controller) {
            $controller->formConfig = '';
            $myFormConfigPath = '$/np/structure/controllers/mailpartials/config_form.yaml';
            $controller->formConfig = $controller->mergeConfig(
                $controller->formConfig,
                $myFormConfigPath
            );

            //overwrite view
            $viewPath = '$/np/structure/controllers/mailpartials';
            $controller->addViewPath($viewPath);

            //add onDuplicate action
            $controller->addDynamicMethod('onDuplicate',function($recordId = null, $context = null) use($controller){
                
                
                if($recordId = post('id') and $layout = explode('-',post('theme')))
                {
                    
                    $newTheme = strtolower($layout[1]);
                    $model = $controller->formFindModelObject($recordId);
                    
                    list($type,$name,$oldTheme) =  explode('-',$model->code);
                    $code = str_replace($oldTheme,$newTheme,$model->code);;
                    
                    $duplicate = $model->replicate();
                    $duplicate->code = $code;
                    $duplicate->name = $code;
                    $duplicate->save();
                    
                    Flash::success('Duplicate successfully. now, pls update according to your need');
                    return Backend::redirect('system/mailpartials/update/'.$duplicate->id);
                }
               

            });
            // get theme list
            $controller->addDynamicMethod('onSelectThemeForm',function($recordId = null, $context = null) use($controller){
                
                $model = $controller->formFindModelObject($recordId);

                $themes = MailLayout::pluck('name','code');

                return $controller->makePartial('select-theme',['themes'=>$themes,'model'=>$model]);

            });
        });
    }
}
