<?php

namespace Np\Structure\Classes;

use Artisan;
use Cache;
use System\Models\MailPartial;

class NP
{

    public static function findPartialsByPattern($pattern)
    {
        return MailPartial::where('code', 'like', $pattern)->orderBy('name', 'asc')->pluck('name', 'code')->all();
    }

    public static function adminLevelRole()
    {
        return [
            'master-trainer'
        ];
    }

    public static function contents_migration_path()
    {
        return plugins_path() . '/np/contents/updates/';
    }

    public static function getSite($key = null)
    {
        $apiSite = request('site');
        $site = !empty($apiSite) ? $apiSite : session('site');

        if ($key != null and isset($site[$key]))
            return $site[$key];

        return $site;
    }

    public static function getSiteId()
    {
        return self::getSite('id');
    }

    public static function getUserId()
    {
        return session('user.id');
    }

    public static function getViewCode($pageType, $view_code)
    {
        $view_code = self::CamelCaseToSnakeCase($view_code);
        $view_code = $pageType . '-' . $view_code . '-default';
        return $view_code;
    }

    public static function getFormUrl($code)
    {
        $formattedUri = implode('-', array_slice(explode('-', $code), 1, -1));
        return '/form/' . str_replace('_', '-', $formattedUri);
    }

    public static function getViewUrl($view_code)
    {
        $parts = explode('-', $view_code);
        $view_code = isset($parts[1]) ? $parts[1] : $view_code;
        return '/site/view/' . $view_code;
    }
    public static function getDetailsUrl($model, $slug)
    {
        $parts = explode('/', $model);
        $modelCode = strtolower(end($parts));
        return '/site/' . $modelCode . '/' . $slug;
    }
    public static function getPreviewDomain()
    {
        $domains =  session('site.domains');
        return isset($domains[0]) ? $domains[0]['fqdn'] : '';
    }

    public static function CamelCaseToSnakeCase($input)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    public static function gets($model, $relation, $key)
    {
        $model = "Np\\Contents\\Models\\" . $model;
        $key = $relation . "." . $key;
        $class = call_user_func_array(array($model, 'with'), [$relation]);
        $class = call_user_func(array($class, 'get'));
        $class = call_user_func_array(array($class, 'groupBy'), [$key]);
        return $class;
    }
    public static function detailsLink($lang, $code, $slug)
    {
        $code = self::CamelCaseToSnakeCase($code);
        return '/' . $lang . '/site/' . $code . '/' . $slug;
    }

    public static function viewLink($code, $lang)
    {
        $code = self::CamelCaseToSnakeCase($code);
        return '/' . $lang . '/site/view/' . $code;
    }

    public static function defaultLocales()
    {
        $defaults = [
            'bn' => 'Bangla',
            'en' => 'English'
        ];

        return $defaults;
    }

    public static function defaultTheme()
    {
        return 'np-default';
    }

    public static function setTenantConnection($site)
    {
        $cluster = $site->cluster;

        $dbConfig = [

            'host' => $cluster->host,
            'username' => $cluster->username,
            'password' => $cluster->password,
            'database' => $site['db_id']

        ];

        self::setTenantConnectionByArray($dbConfig);
    }

    public static function setTenantConnectionByArray($configuration = array())
    {

        $key = 'database.connections.tenant';
        $db = config($key);
        $db['host'] = $configuration['host'];
        $db['username'] = $configuration['username'];
        $db['password'] = $configuration['password'];
        $db['database'] = $configuration['database'];
        config(
            [$key => $db]
        );
    }

    public static function generate_uuid()
    {
        $data = openssl_random_pseudo_bytes(16, $secure);
        if (false === $data) {
            return false;
        }
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public static function clearCache($site = null)
    {
        if (!is_null($site)) {
            $siteId = $site->id;
            $cacheTag = '_site_' . $siteId;
            Cache::tags($cacheTag)->flush();
        } else
            Artisan::call('cache:clear');
    }
}
