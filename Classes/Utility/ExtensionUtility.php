<?php

namespace Hoogi91\Charts\Utility;

use Hoogi91\Spreadsheets\Utility\ExtensionManagementUtility;

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

    /**
     * Easy check whether spreadsheet extension is loaded and has direction support
     *
     * @return bool
     * @throws \TYPO3\CMS\Core\Package\Exception
     */
    public static function hasSpreadsheetExtensionWithDirectionSupport(): bool
    {
        $extVersion = ExtensionManagementUtility::getExtensionVersion('spreadsheets');
        return !empty($extVersion) && version_compare($extVersion, '1.1', '>=');
    }

    /**
     * Easy check whether spreadsheet extension is NOT loaded or has NOT direction support
     *
     * @return bool
     * @throws \TYPO3\CMS\Core\Package\Exception
     */
    public static function missesSpreadsheetExtensionOrDirectionSupport(): bool
    {
        return !self::hasSpreadsheetExtensionWithDirectionSupport();
    }
}
