<?php

namespace Hoogi91\Charts\Utility;

use TYPO3\CMS\Core\Package\Exception as PackageException;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

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
     * @param mixed $defaultValue
     *
     * @return array|mixed|null
     */
    public static function getConfig($key = '', $defaultValue = null)
    {
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['charts'])) {
            static::$extConf = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['charts'];
        }
        if (empty(static::$extConf)) {
            static::$extConf = unserialize(
                $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['charts'],
                ['allowed_classes' => false]
            );
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
     */
    public static function hasSpreadsheetExtensionWithDirectionSupport(): bool
    {
        try {
            $extVersion = ExtensionManagementUtility::getExtensionVersion('spreadsheets');
            return !empty($extVersion) && version_compare($extVersion, '1.1', '>=');
        } catch (PackageException $exception) {
            return true; // from now on if ext version is missing we force that spreadsheet ext has direction support
        }
    }

    /**
     * Easy check whether spreadsheet extension is NOT loaded or has NOT direction support
     *
     * @return bool
     */
    public static function missesSpreadsheetExtensionOrDirectionSupport(): bool
    {
        return !self::hasSpreadsheetExtensionWithDirectionSupport();
    }
}
