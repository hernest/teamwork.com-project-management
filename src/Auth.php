<?php

declare(strict_types = 1);

namespace TeamWorkPm;

class Auth
{
    const DEFAULT_URL = 'https://authenticate.teamwork.com/';

    private static string $url = self::DEFAULT_URL;

    private static array $config = [
        'url' => null,
        'key' => null,
    ];

    private static bool $is_subdomain = false;

    public static function set(string ...$args): void
    {
        // Reset to defaults when setting new auth
        // This is needed when running in a job queue with different credentials
        self::$is_subdomain = false;
        self::$url = self::DEFAULT_URL;
        self::$config = [
            'url' => null,
            'key' => null
        ];
        
        $numArgs = count($args);
        if ($numArgs === 1) {
            static::$config['url'] = static::$url;
            static::$config['key'] = $args[0];
            static::$config['url'] = Factory::account()->authenticate()->url;
        } elseif ($numArgs === 2) {
            static::$config['url'] = $url = $args[0];
            static::checkSubDomain($url);
            if (static::$is_subdomain) {
                static::$config['url'] = static::$url;
            }
            static::$config['key'] = $args[1];
            if (static::$is_subdomain) {
                $url = Factory::account()->authenticate()->url;
            }
            static::$config['url'] = $url;
        }
    }

    public static function get(): array
    {
        return array_values(static::$config);
    }

    private static function checkSubDomain(string $url): void
    {
        $eu_domain = strpos($url, '.eu');

        if ($eu_domain !== false) {
            static::$url = 'https://authenticate.eu.teamwork.com/';
            $url = substr($url, 0, $eu_domain);
        }
        if (!str_contains($url, '.')) {
            static::$is_subdomain = true;
        }
    }
}
