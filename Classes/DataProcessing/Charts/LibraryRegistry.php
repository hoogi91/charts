<?php

namespace Hoogi91\Charts\DataProcessing\Charts;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Hoogi91\Charts\RegisterChartLibraryException;

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
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * LibraryRegistry constructor.
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @param string $name
     * @param string $class
     * @param bool   $override
     *
     * @throws RegisterChartLibraryException
     */
    public function register($name, $class, $override = false)
    {
        if ($override === false && array_key_exists($name, $this->classMap)) {
            throw new RegisterChartLibraryException(sprintf(
                'Registration of chart library "%s" failed cause it\'s key/name "%s" is already in use.',
                $class,
                $name
            ), 1522149364);
        }

        $interfaces = class_implements($class);
        if (!in_array(LibraryInterface::class, $interfaces)) {
            throw new RegisterChartLibraryException(sprintf(
                'Registration of chart library "%s" failed cause it doesn\'t implement "%s".',
                $class,
                LibraryInterface::class
            ), 1522149372);
        }

        // add new library to class map
        $this->classMap[$name] = $class;
    }

    /**
     * @param string $name
     *
     * @return null|object
     */
    public function getLibrary($name)
    {
        if (!array_key_exists($name, $this->classMap)) {
            return null;
        }

        /** @var LibraryInterface $object */
        $libraryInstance = $this->objectManager->get($this->classMap[$name]);
        return $libraryInstance;
    }

    /**
     * @return string
     */
    public function getLibrarySelect($data)
    {
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
