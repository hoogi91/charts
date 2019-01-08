<?php

namespace Hoogi91\Charts\DataProcessing\Charts;

use TYPO3\CMS\Core\Page\PageRenderer;
use Hoogi91\Charts\Domain\Model\ChartData;

/**
 * Interface LibraryInterface
 * @package Hoogi91\Charts\DataProcessing\Charts
 */
interface LibraryInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * return stylesheet file paths and render them inside pageRenderer if available
     *
     * @param string       $chartType
     * @param PageRenderer $pageRenderer
     *
     * @return array
     */
    public function getStylesheetAssets($chartType, $pageRenderer = null);

    /**
     * return javascript file paths and render them inside pageRenderer if available
     *
     * @param string       $chartType
     * @param PageRenderer $pageRenderer
     *
     * @return array
     */
    public function getJavascriptAssets($chartType, $pageRenderer = null);

    /**
     * return entity stylesheet and render it inside pageRenderer if available
     *
     * @param string       $chartIdentifier
     * @param string       $chartType
     * @param ChartData    $chartEntity
     * @param PageRenderer $pageRenderer
     *
     * @return string
     */
    public function getEntityStylesheet($chartIdentifier, $chartType, $chartEntity, $pageRenderer = null);

    /**
     * return entity javascript and render it inside pageRenderer if available
     *
     * @param string       $chartIdentifier
     * @param string       $chartType
     * @param ChartData    $chartEntity
     * @param PageRenderer $pageRenderer
     *
     * @return string
     */
    public function getEntityJavascript($chartIdentifier, $chartType, $chartEntity, $pageRenderer = null);
}
