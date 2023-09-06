<?php

namespace Np\Api\Twig;


use Twig;
use Markdown;
use System\Models\MailPartial;
use System\Models\MailBrandSetting;
use System\Classes\MarkupManager;
use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
use Illuminate\Support\Facades\DB;
use View;
use Illuminate\Support\Facades\Log;

class ThemeManager
{

    /**
     * @var array Cache of registration callbacks.
     */
    protected $callbacks = [];

    /**
     * @var array A cache of customised mail templates.
     */
    protected $templateCache = [];

    /**
     * @var array List of registered templates in the system
     */
    protected $registeredTemplates;

    /**
     * @var array List of registered partials in the system
     */
    protected $registeredPartials;

    /**
     * @var array List of registered layouts in the system
     */
    protected $registeredLayouts;

    /**
     * @var bool Internal marker for rendering mode
     */
    protected $isHtmlRenderMode = false;

    /**
     * @var bool Internal marker for booting custom twig extensions.
     */
    protected $isTwigStarted = false;

    use \October\Rain\Support\Traits\Singleton;
    public $lang = 'bn';


    public function getValueByKey($key, array $data, $default = null)
    {
        // @assert $key is a non-empty string
        // @assert $data is a loopable array
        // @otherwise return $default value
        if (!is_string($key) || empty($key) || !count($data)) {
            return $default;
        }

        // @assert $key contains a dot notated string
        if (strpos($key, '.') !== false) {
            $keys = explode('.', $key);

            foreach ($keys as $innerKey) {
                // @assert $data[$innerKey] is available to continue
                // @otherwise return $default value
                if (!array_key_exists($innerKey, $data)) {
                    return $default;
                }

                $data = $data[$innerKey];
            }

            return $data;
        }


        // @fallback returning value of $key in $data or $default value
        return isset($data[$key]) ? $data[$key] : (request($key) ? request($key) : $default);
    }

    public function paramsBindings($params, $bindings = [])
    {
        $params = array_map(function ($param) use ($bindings) {

            if (starts_with($param, '?')) {
                $key = str_replace('?', '', $param);
                //return isset($bindings[$key]) ? $bindings[$key] : $param;
                return $this->getValueByKey($key, $bindings, $param);
            }
            return $param;
        }, $params);


        return $params;
    }
    public function parseQueryJson($json, $bindings = [])
    {



        $json = trim(str_replace('\\', '\\\\', $json));
        $partialProperties = json_decode($json);

        if (json_last_error() != JSON_ERROR_NONE)
            return [];


        foreach ($partialProperties as $k => $v) {


            $class = $v->class;

            if (isset($v->methods) and count($v->methods))
                foreach ($v->methods as $method) {
                    $methodName = $method->name;
                    if (isset($method->params) and count($method->params)) {
                        $params = $this->paramsBindings($method->params, $bindings);
                        $class = call_user_func_array(array($class, $methodName), $params);
                    } else
                        $class = call_user_func(array($class, $methodName));
                }
        }

        return [$k => $class];
    }
    public function render($content, $data = [])
    {
        if (!$content) {
            return '';
        }

        $html = $this->renderTwig($content, $data);

        $html = Markdown::parseSafe($html);

        return $html;
    }

    public function checkJsonQueryData($template, &$data)
    {

        $queryJson = isset($template->content_text) ? $template->content_text : null;

        $query_data = [];
        if ($queryJson)
            $query_data = $this->parseQueryJson($queryJson, $data);

        $globalVars = View::getShared();
        $data = array_merge($data, $query_data, $globalVars, ['common_data' => $globalVars]);
    }

    public function errorRenderTemplate($template, $data){
        $content = isset($template->content_html) ? $template->content_html : null;

        $html = $this->render($content, $data);
        return $html;
    }

    public function renderTemplate($template, $data = [])
    {

        $this->isHtmlRenderMode = true;

        $this->checkJsonQueryData($template, $data);

        $content = isset($template->content_html) ? $template->content_html : null;

        $html = $this->render($content, $data);

        $css = MailBrandSetting::renderCss();

        $disableAutoInlineCss = false;

        if (isset($template->layout)) {

            $disableAutoInlineCss = array_get($template->layout->options, 'disable_auto_inline_css', $disableAutoInlineCss);

            $html = $this->renderTwig($template->layout->content_html, [
                'content' => html_entity_decode($html),
                'css' => $template->layout->content_css,
                'brandCss' => $css
            ] + (array) $data);

            $css .= PHP_EOL . $template->layout->content_css;
        }

        if (!$disableAutoInlineCss) {
            $html = (new CssToInlineStyles)->convert($html, $css);
        }

        return $html;
    }

    /**
     * Render the Markdown template into text.
     * @param $content
     * @param array $data
     * @return string
     */
    public function renderText($content, $data = [])
    {
        if (!$content) {
            return '';
        }

        $text = $this->renderTwig($content, $data);

        $text = html_entity_decode(preg_replace("/[\r\n]{2,}/", "\n\n", $text), ENT_QUOTES, 'UTF-8');

        return $text;
    }

    public function renderTextTemplate($template, $data = [])
    {
        $this->isHtmlRenderMode = false;

        $templateText = $template->content_text;

        if (!strlen($template->content_text)) {
            $templateText = $template->content_html;
        }

        $text = $this->renderText($templateText, $data);

        if ($template->layout) {
            $text = $this->renderTwig($template->layout->content_text, [
                'content' => $text
            ] + (array) $data);
        }

        return $text;
    }

    public function renderPartial($code, array $params = [])
    {

        //check existance of theme specific partial
        $themeCode = isset($params['request']['site']['theme_code']) ? $params['request']['site']['theme_code'] : false;
        $themeSpecificPartial = false;
        if ($themeCode and $parts = explode('-', $themeCode)) {

            $themeName = strtolower($parts[1]);
            $newCode = str_replace('default', $themeName, $code);
            $themeSpecificPartial = MailPartial::where('code', $newCode)->first();
        }

        if ($themeCode && $themeSpecificPartial) {

            $partial = $themeSpecificPartial;
        } elseif (!$partial = MailPartial::findOrMakePartial($code)) {
            return '<!-- Missing partial: ' . $code . ' -->';
        }

        $this->checkJsonQueryData($partial, $params);

        if ($this->isHtmlRenderMode) {
            //$content = isset($params['lang']) and ($params['lang'] == 'en') ? $partial->content_html_en ?: $partial->content_html : $partial->content_html;
            $content = $partial->content_html;
            // if (isset($params['lang']) and ($params['lang'] == 'en') and $partial->content_html_en) {
            //     $content = $partial->content_html_en;
            // }
        } else {
            $content = $partial->content_text ?: $partial->content_html;
        }

        if (!strlen(trim($content))) {
            return '';
        }

        return $this->renderTwig($content, $params);
    }

    /**
     * Internal helper for rendering Twig
     */
    protected function renderTwig($content, $data = [])
    {
        if ($this->isTwigStarted) {
            return Twig::parse($content, $data);
        }

        $this->startTwig();

        $result = Twig::parse($content, $data);

        $this->stopTwig();

        return $result;
    }

    /**
     * Temporarily registers mail based token parsers with Twig.
     * @return void
     */
    protected function startTwig()
    {
        if ($this->isTwigStarted) {
            return;
        }

        $this->isTwigStarted = true;

        $markupManager = MarkupManager::instance();
        $markupManager->beginTransaction();
        $markupManager->registerTokenParsers([
            new ThemePartialTokenParser
        ]);
    }

    /**
     * Indicates that we are finished with Twig.
     * @return void
     */
    protected function stopTwig()
    {
        if (!$this->isTwigStarted) {
            return;
        }

        $markupManager = MarkupManager::instance();
        $markupManager->endTransaction();

        $this->isTwigStarted = false;
    }

    //
    // Registration
    //

    /**
     * Loads registered mail templates from modules and plugins
     * @return void
     */
    public function loadRegisteredTemplates()
    {
        foreach ($this->callbacks as $callback) {
            $callback($this);
        }

        $plugins = PluginManager::instance()->getPlugins();
        foreach ($plugins as $pluginId => $pluginObj) {
            $layouts = $pluginObj->registerMailLayouts();
            if (is_array($layouts)) {
                $this->registerMailLayouts($layouts);
            }

            $templates = $pluginObj->registerMailTemplates();
            if (is_array($templates)) {
                $this->registerMailTemplates($templates);
            }

            $partials = $pluginObj->registerMailPartials();
            if (is_array($partials)) {
                $this->registerMailPartials($partials);
            }
        }
    }

    /**
     * Returns a list of the registered templates.
     * @return array
     */
    public function listRegisteredTemplates()
    {
        if ($this->registeredTemplates === null) {
            $this->loadRegisteredTemplates();
        }

        return $this->registeredTemplates;
    }

    /**
     * Returns a list of the registered partials.
     * @return array
     */
    public function listRegisteredPartials()
    {
        if ($this->registeredPartials === null) {
            $this->loadRegisteredTemplates();
        }

        return $this->registeredPartials;
    }

    /**
     * Returns a list of the registered layouts.
     * @return array
     */
    public function listRegisteredLayouts()
    {
        if ($this->registeredLayouts === null) {
            $this->loadRegisteredTemplates();
        }

        return $this->registeredLayouts;
    }

    /**
     * Registers a callback function that defines mail templates.
     * The callback function should register templates by calling the manager's
     * registerMailTemplates() function. Thi instance is passed to the
     * callback function as an argument. Usage:
     *
     *     MailManager::registerCallback(function($manager) {
     *         $manager->registerMailTemplates([...]);
     *     });
     *
     * @param callable $callback A callable function.
     */
    public function registerCallback(callable $callback)
    {
        $this->callbacks[] = $callback;
    }

    /**
     * Registers mail views and manageable templates.
     */
    public function registerMailTemplates(array $definitions)
    {
        if (!$this->registeredTemplates) {
            $this->registeredTemplates = [];
        }

        // Prior sytax where (key) code => (value) description
        if (!isset($definitions[0])) {
            $definitions = array_keys($definitions);
        }

        $definitions = array_combine($definitions, $definitions);

        $this->registeredTemplates = $definitions + $this->registeredTemplates;
    }

    /**
     * Registers mail views and manageable layouts.
     */
    public function registerMailPartials(array $definitions)
    {
        if (!$this->registeredPartials) {
            $this->registeredPartials = [];
        }

        $this->registeredPartials = $definitions + $this->registeredPartials;
    }

    /**
     * Registers mail views and manageable layouts.
     */
    public function registerMailLayouts(array $definitions)
    {
        if (!$this->registeredLayouts) {
            $this->registeredLayouts = [];
        }

        $this->registeredLayouts = $definitions + $this->registeredLayouts;
    }
}
