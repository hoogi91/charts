<?php

namespace Hoogi91\Charts\DataProcessing\Charts;

use Hoogi91\Charts\Domain\Model\ChartData;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Interface LibraryInterface
 * @package Hoogi91\Charts\DataProcessing\Charts
 */
interface LibraryInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * return stylesheet file paths and render them inside pageRenderer if available
     *
     * @param string $chartType
     * @param PageRenderer $pageRenderer
     *
     * @return array
     */
    public function getStylesheetAssets($chartType, $pageRenderer = null): array;

    /**
     * return javascript file paths and render them inside pageRenderer if available
     *
     * @param string $chartType
     * @param PageRenderer $pageRenderer
     *
     * @return array
     */
    public function getJavascriptAssets($chartType, $pageRenderer = null): array;

    /**
     * return entity stylesheet and render it inside pageRenderer if available
     *
     * @param string $chartIdentifier
     * @param string $chartType
     * @param ChartData $chartEntity
     * @param PageRenderer $pageRenderer
     *
     * @return string
     */
    public function getEntityStylesheet($chartIdentifier, $chartType, $chartEntity, $pageRenderer = null): string;

    /**
     * return entity javascript and render it inside pageRenderer if available
     *
     * @param string $chartIdentifier
     * @param string $chartType
     * @param ChartData $chartEntity
     * @param PageRenderer $pageRenderer
     *
     * @return string
     */
    public function getEntityJavascript($chartIdentifier, $chartType, $chartEntity, $pageRenderer = null): string;
}
