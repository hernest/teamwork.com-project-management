<?php

namespace TeamWorkPm;

class Auth
{
    const DEFAULT_URL = 'https://authenticate.teamwork.com/';
    
    private static $url = self::DEFAULT_URL;

    private static $config = [
        'url' => null,
        'key' => null
    ];

    private static $is_subdomain = false;

    public static function set()
    {
        // Reset to defaults when setting new auth
        self::$is_subdomain = false;
        self::$url = self::DEFAULT_URL;
        self::$config = [
            'url' => null,
            'key' => null
        ];
        
        $num_args = func_num_args();
        if ($num_args === 1) {
            self::$config['url'] = self::$url;
            self::$config['key'] = func_get_arg(0);
            self::$config['url'] = Factory::build('account')->authenticate()->url;
        } elseif ($num_args === 2) {
            self::$config['url'] = $url = func_get_arg(0);
            self::checkSubDomain($url);
            if (self::$is_subdomain) {
                self::$config['url'] = self::$url;
            }
            self::$config['key']  = func_get_arg(1);
            if (self::$is_subdomain) {
                $test = Factory::build('account')->authenticate();
                $url = $test->url;
            }
            self::$config['url'] = $url;
        }
    }

    public static function get()
    {
        return array_values(self::$config);
    }

    private static function checkSubDomain($url)
    {
        $eu_domain = strpos($url, '.eu');

        if ($eu_domain !== false) {
            self::$url = 'https://authenticate.eu.teamwork.com/';
            $url = substr($url, 0, $eu_domain);
        }
        if (strpos($url, '.') === false) {
            self::$is_subdomain = true;
        }
    }
}
