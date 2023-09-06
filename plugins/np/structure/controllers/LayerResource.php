<?php

namespace Np\Structure\Controllers;

use BackendMenu;
use Np\Structure\Controllers\NpBaseController;
use Np\Structure\Models\Block;
use Np\Structure\Models\ContentType;
use Np\Structure\Models\Layer;
use Np\Structure\Models\LayerResource as ModelLayerResource;
use Np\Structure\Models\Ministry;
use Np\Structure\Models\TexonomyType;
use October\Rain\Support\Facades\Flash;
use System\Models\MailPartial;

class LayerResource extends NpBaseController
{

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Structure', 'np-structure', 'np-layer-resource');
    }

    private function prepareVariables()
    {


        $ministry_id = post('ministry_id', 0);

        $ministries = Ministry::get()->all();

        $layers = Layer::when($ministry_id, function ($query) use ($ministry_id) {

            return $query->where('code', '!=', 'ministry');
        })->get()->all();

        $contentTypes = ContentType::when($ministry_id, function ($query) {
        })->get()->all();

        $blocks = Block::get()->all();
        $taxonomies = TexonomyType::get()->all();
        $forms = MailPartial::where('code', 'like', 'form-%')->orderBy('name', 'asc')->get();


        $savedContentTypes = ModelLayerResource::where('ministry_id', $ministry_id)->get()->toArray();

        $data = [];

        foreach ($savedContentTypes as $item) {
            $data['content_types'][$item['layer_id']] = is_array($item['content_types']) ? array_flip($item['content_types']) : [];
            $data['blocks'][$item['layer_id']] = is_array($item['blocks']) ? array_flip($item['blocks']) : [];
            $data['forms'][$item['layer_id']] = is_array($item['forms']) ? array_flip($item['forms']) : [];
        }

        //dd($data);
        //dd($savedContentTypes);
        $this->vars['saved_resources'] = $data;
        $this->vars['ministries'] = $ministries;
        $this->vars['layers'] = $layers;
        $this->vars['content_types'] = $contentTypes;
        $this->vars['blocks'] = $blocks;
        $this->vars['taxonomies'] = $taxonomies;
        $this->vars['forms'] = $forms;
    }

    public function index()
    {
        $this->pageTitle = 'Layer Resource Mapping';
        $this->prepareVariables();
    }

    public function onSaveLayerResource()
    {
        $layers = Layer::all()->toArray();

        $ministry_id = post('ministry_id', 0);
        $resource = post('resource');
        $data = post($resource, []);

        foreach ($layers as $layer) {

            $layerId = $layer['id'];

            $items = isset($data[$layerId]) ? $data[$layerId] : null;

            $whereCondition = [
                'ministry_id' => $ministry_id,
                'layer_id' => $layerId
            ];

            ModelLayerResource::updateOrCreate(
                $whereCondition,
                [
                    "$resource" => $items
                ]
            );
        }


        Flash::success('Data saved successfully!');
    }

    public function onSelectMinistry()
    {

        $this->prepareVariables();
        $this->vars['ministry_id'] = post('ministry_id');

        return [
            '#ministryLayerResource' => $this->makePartial('ministry_layer_resources')
        ];
    }
}
