<?php namespace {{ pluginNamespace }}\Controllers;

use BackendMenu;
use Np\Structure\Controllers\NpBaseController;

class {{ controller }} extends NpBaseController
{
    public $implement = [{% for behavior in behaviors %}
        '{{ behavior }}'{% if not loop.last %},{% endif %}{% endfor %}
    ];
    {{ templateParts|raw }}
    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Np.Contents', 'np-contents', 'np-contents');
{% if menuItem %}
{% if not sideMenuItem %}
        BackendMenu::setContext('{{ pluginCode }}', '{{ menuItem }}', '{{ sideMenuItem }}');
{% else %}
        BackendMenu::setContext('{{ pluginCode }}', '{{ menuItem }}', '{{ sideMenuItem }}');
{% endif %}
{% endif %}
    }
}
