<?php

namespace Hoogi91\Charts\DataProcessing\Charts;

use Hoogi91\Charts\Domain\Model\ChartData;
use TYPO3\CMS\Core\Page\PageRenderer;

interface LibraryInterface
{

    public static function getServiceIndex(): string;

    public function getName(): string;

    public function getStylesheetAssets(string $chartType, PageRenderer $pageRenderer = null): array;

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
