<?php

namespace Hoogi91\Charts\Controller\Wizard;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class TableController
 * @package Hoogi91\Charts\Controller\Wizard
 */
class TableController extends \TYPO3\CMS\Backend\Controller\Wizard\TableController
{
    /**
     * Will get and return the configuration code string
     * Will also save (and possibly redirect/exit) the content if a save button has been pressed
     *
     * @param array $row Current parent record row
     *
     * @return array Table config code in an array
     */
    public function getConfigCode($row)
    {
        return $this->fixEmptyConfiguration(parent::getConfigCode($row));
    }

    /**
     * Will get and return the configuration code string
     * Will also save (and possibly redirect/exit) the content if a save button has been pressed
     *
     * @param array $row Current parent record row
     * @param ServerRequestInterface $request
     * @return array|ResponseInterface Table config code in an array
     */
    protected function getConfiguration(array $row, ServerRequestInterface $request)
    {
        return $this->fixEmptyConfiguration(parent::getConfiguration($row, $request));
    }

    /**
     * This will ensure to always return a filled array with empty values => otherwise wizard is not shown ;)
     *
     * @param array $configuration
     * @return array
     */
    private function fixEmptyConfiguration(array $configuration): array
    {
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
