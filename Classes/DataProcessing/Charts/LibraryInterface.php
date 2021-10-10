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
     * @param PageRenderer|null $pageRenderer
     *
     * @return array
     */
    public function getStylesheetAssets(string $chartType, PageRenderer $pageRenderer = null): array;

    /**
     * return javascript file paths and render them inside pageRenderer if available
     *
     * @param string $chartType
     * @param PageRenderer|null $pageRenderer
     *
     * @return array
     */
    public function getJavascriptAssets(string $chartType, PageRenderer $pageRenderer = null): array;

    /**
     * return entity stylesheet and render it inside pageRenderer if available
     *
     * @param string $chartIdentifier
     * @param string $chartType
     * @param ChartData $chartEntity
     * @param PageRenderer|null $pageRenderer
     *
     * @return string
     */
    public function getEntityStylesheet(
        string $chartIdentifier,
        string $chartType,
        ChartData $chartEntity,
        PageRenderer $pageRenderer = null
    ): string;

    /**
     * return entity javascript and render it inside pageRenderer if available
     *
     * @param string $chartIdentifier
     * @param string $chartType
     * @param ChartData $chartEntity
     * @param PageRenderer|null $pageRenderer
     *
     * @return string
     */
    public function getEntityJavascript(
        string $chartIdentifier,
        string $chartType,
        ChartData $chartEntity,
        PageRenderer $pageRenderer = null
    ): string;
}
