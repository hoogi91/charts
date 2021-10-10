<?php

namespace Hoogi91\Charts\DataProcessing\Charts;

use Hoogi91\Charts\RegisterChartLibraryException;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class LibraryRegistry
 * @package Hoogi91\Charts\DataProcessing\Charts
 */
class LibraryRegistry implements SingletonInterface
{
    /**
     * Holds the mapping key => className
     * @var array
     */
    protected $classMap = [];

    /**
     * @param string $name
     * @param string $class
     * @param bool $override
     *
     * @throws RegisterChartLibraryException
     */
    public function register(string $name, string $class, bool $override = false): void
    {
        if (($override === false && array_key_exists($name, $this->classMap)) || class_exists($class) === false) {
            throw new RegisterChartLibraryException(
                sprintf(
                    'Registration of chart library "%s" failed cause it\'s key/name "%s" is already in use.',
                    $class,
                    $name
                ),
                1522149364
            );
        }

        $interfaces = class_implements($class);
        if (!in_array(LibraryInterface::class, $interfaces, true)) {
            throw new RegisterChartLibraryException(
                sprintf(
                    'Registration of chart library "%s" failed cause it doesn\'t implement "%s".',
                    $class,
                    LibraryInterface::class
                ),
                1522149372
            );
        }

        // add new library to class map
        $this->classMap[$name] = $class;
    }

    /**
     * @param string $name
     *
     * @return LibraryInterface|null
     */
    public function getLibrary(string $name): ?LibraryInterface
    {
        if (array_key_exists($name, $this->classMap) === false) {
            return null;
        }
        $library = GeneralUtility::makeInstance($this->classMap[$name]);
        return $library instanceof LibraryInterface ? $library : null;
    }

    /**
     * @param array $data
     * @return string
     */
    public function getLibrarySelect(array $data): string
    {
        // ensure loading of extension configuration before creating library select
        if (!isset($GLOBALS['PHPUNIT_TESTING']) || $GLOBALS['PHPUNIT_TESTING'] !== true) {
            ExtensionManagementUtility::loadExtLocalconf(); // @codeCoverageIgnore
        }

        $html = '<div class="form-inline">';
        $html .= sprintf('<input type="hidden" name="%s" value="%s"/>', $data['fieldName'], $data['fieldValue']);
        $html .= sprintf('<select class="form-control" name="%s">', $data['fieldName']);
        foreach ($this->classMap as $name => $class) {
            if ($name === $data['fieldValue']) {
                $html .= sprintf('<option value="%1$s" selected="selected">%1$s (%2$s)</option>', $name, $class);
            } else {
                $html .= sprintf('<option value="%1$s">%1$s (%2$s)</option>', $name, $class);
            }
        }
        $html .= '</select>';
        $html .= '</div>';
        return $html;
    }
}
