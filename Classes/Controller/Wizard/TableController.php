<?php

namespace Hoogi91\Charts\Controller\Wizard;

/**
 * Class TableController
 * @package Hoogi91\Charts\Controller\Wizard
 */
class TableController extends \TYPO3\CMS\Backend\Controller\Wizard\TableController
{
    /**
     * Will get and return the configuration code string
     * Will also save (and possibly redirect/exit) the content if a save button has been pressed
     * Will always return a fille array with empty values => otherwise wizard is not shown ;)
     *
     * @param array $row Current parent record row
     *
     * @return array Table config code in an array
     */
    public function getConfigCode($row)
    {
        $configuration = parent::getConfigCode($row);
        // only fix empty configuration arrays if xml storage is active
        if ($this->xmlStorage) {
            // return default array => one row with 4 empty columns if config is completly empty
            if (empty($configuration)) {
                return [['', '', '', '']];
            }

            // if configuration is an array we equalize row value count ;)
            if (is_array($configuration)) {
                $largestRowCount = max(array_map('count', $configuration));
                foreach ($configuration as $k => $row) {
                    if (empty($row)) {
                        $configuration[$k] = array_fill(0, $largestRowCount, '');
                    }
                }
            }
        }
        return $configuration;
    }
}
