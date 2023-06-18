<?php

declare(strict_types=1);

namespace Hoogi91\Charts\DataProcessing\Charts;

use Hoogi91\Charts\Domain\Model\ChartData;
use TYPO3\CMS\Core\Page\PageRenderer;

interface LibraryInterface
{
    public static function getServiceIndex(): string;

    public function getName(): string;

    /**
     * @return array<mixed>
     */
    public function getStylesheetAssets(string $chartType, PageRenderer $pageRenderer = null): array;

    /**
     * @return array<mixed>
     */
    public function getJavascriptAssets(string $chartType, PageRenderer $pageRenderer = null): array;

    public function getEntityStylesheet(
        string $chartIdentifier,
        string $chartType,
        ChartData $chartEntity,
        PageRenderer $pageRenderer = null
    ): string;

    public function getEntityJavascript(
        string $chartIdentifier,
        string $chartType,
        ChartData $chartEntity,
        PageRenderer $pageRenderer = null
    ): string;
}
