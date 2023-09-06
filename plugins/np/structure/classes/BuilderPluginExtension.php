<?php
namespace Np\Structure\Classes;

use Event;
use Backend\FormWidgets\RecordFinder;
use Np\Contents\Models\NoticeCategory;
use RainLab\Builder\Behaviors\IndexModelOperations;
use RainLab\Builder\Classes\ModelModel;
use October\Rain\Support\Facades\Yaml;

/**
 * Class BackendUserExtension
 * @package Renatio\Logout\Classes
 */
class BuilderPluginExtension
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->extendModelCreationForm();
        $this->extendRecordFinderWidget();
    }
    /**
     * @return void
     */

    public function extendModelCreationForm()
    {
        Event::listen('backend.form.extendFields', function ($widget) {

            if (!$widget->model instanceof ModelModel) {
                return;
            }
            $widget->removeField('className');
            $widget->removeField('databaseTable');
            $widget->removeField('addTimestamps');
            $widget->removeField('addSoftDeleting');

            $fieldsConfigFile = plugins_path() . '/np/structure/classes/modelmodel/fields.yaml';;
            $fields = Yaml::parseFile($fieldsConfigFile);
            $widget->addFields($fields['fields']);
        });
    }
    public function extendRecordFinderWidget()
    {
        RecordFinder::extend(function ($widget) {
            $widget->vars['useCreate'] = $widget->config->createRecord;
            $widget->addViewPath(plugins_path() . '/np/structure/formwidgets/recordfinder/partials');
            $widget->addDynamicMethod('onCreateForm', function () use ($widget) {

                $widget->init();
                $config = $widget->makeConfig($widget->config->formyaml);
                $config->model = new $widget->config->modelClass;
                $formWidget = $widget->makeWidget('Backend\Widgets\Form', $config);
                //$widget->prepareVars();

                $widget->vars['title'] = $widget->config->label;
                $widget->vars['formWidget'] = $formWidget;
                $widget->vars['formyaml'] = $widget->config->formyaml;
                $widget->vars['modelClass'] = $widget->config->modelClass;


                $partial = dirname(__DIR__) . '/formwidgets/recordfinder/partials/recordfinder_create_form';
                return $widget->makePartial($partial);
            });

            $widget->addDynamicMethod('onCreate', function () use ($widget) {
                $formyaml = trim(post('formyaml'));
                $modelClass = post('modelClass');

                $config = $widget->makeConfig($formyaml);
                $model = $config->model = new $modelClass;
                $formWidget = $widget->makeWidget('Backend\Widgets\Form', $config);
                $formWidget->bindToController();
                $data = $formWidget->getSaveData();
                foreach ($data as $key => $value) {
                    $model->{$key} = $value;
                }
                $model->save();
                $widget->vars['value'] = $model->id;
                $widget->vars['value'] = $widget->getKeyValue();
                $widget->vars['field'] = $formWidget->formField;
                $widget->vars['nameValue'] = $widget->getNameValue();

                return [
                    'id' => $model->id,
                    'htmlid' => $widget->getId(),
                    'key' => $widget->keyFrom,
                ];
            });
        });
    }
}
