<?php

namespace Hoogi91\Charts\Controller\Wizard;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class TableController
 * @package Hoogi91\Charts\Controller\Wizard
 * @deprecated Remove fix after TYPO3 v10 support ends
 */
class TableController extends \TYPO3\CMS\Backend\Controller\Wizard\TableController
{

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
        $config = parent::getConfiguration($row, $request);

        return is_array($config) ? $this->fixEmptyConfiguration($config) : $config;
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
            // return default array => one row with 4 empty columns if config is completely empty
            if (empty($configuration)) {
                return [['', '', '', '']];
            }

            // if configuration is an array => equalize row value count
            $largestRowCount = max(array_map('count', $configuration));
            foreach ($configuration as $k => $row) {
                $configuration[$k] = array_replace(array_fill(0, $largestRowCount, ''), array_values($row));
                ksort($configuration[$k]);
            }
        }
        return $configuration;
    }
}
