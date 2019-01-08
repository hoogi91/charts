<?php

namespace Hoogi91\Charts\Utility;

/**
 * Class ExtensionUtility
 * @package Hoogi91\Charts\Utility
 */
class ExtensionUtility
{

    /**
     * @var array
     */
    protected static $extConf = [];

    /**
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return array|mixed|null
     */
    public static function getConfig($key = '', $defaultValue = null)
    {
        if (empty(static::$extConf)) {
            static::$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['charts']);
        }

        if (empty($key)) {
            return static::$extConf;
        }

        if (array_key_exists($key, static::$extConf)) {
            return static::$extConf[$key];
        }
        return $defaultValue ?? null;
    }
}
