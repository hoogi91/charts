<?php

declare(strict_types=1);

namespace Hoogi91\Charts\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\LibraryInterface;
use Hoogi91\Charts\Domain\Model\ChartData;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Page\PageRenderer;

use const FILTER_VALIDATE_BOOLEAN;
use const JSON_THROW_ON_ERROR;

abstract class AbstractLibrary implements LibraryInterface
{
    public function __construct(private readonly ExtensionConfiguration $extensionConfiguration)
    {
    }

    protected function getLibraryConfig(string $path, string $default = ''): string
    {
        try {
            $path = str_replace('.', '_', static::getServiceIndex()) . '_' . $path;
            $config = $this->extensionConfiguration->get('charts', $path);

            return is_string($config) ? $config : $default;
        } catch (Exception) {
            return $default;
        }
    }

    /**
     * @return array<mixed>
     */
    public function getStylesheetAssets(string $chartType, PageRenderer $pageRenderer = null): array
    {
        $useAssets = (bool) filter_var($this->getLibraryConfig('assets', 'true'), FILTER_VALIDATE_BOOLEAN);
        if ($useAssets === false) {
            return [];
        }

        $assets = $this->getStylesheetAssetsToLoad();

        // directly include when pageRenderer is given
        if ($pageRenderer instanceof PageRenderer) {
            foreach ($assets as $asset => $options) {
                $pageRenderer->addCssLibrary(
                    $asset,
                    (string) ($options['rel'] ?? 'stylesheet'),
                    (string) ($options['media'] ?? 'all'),
                    (string) ($options['title'] ?? ''),
                    (bool) ($options['compress'] ?? false),
                    (bool) ($options['forceOnTop'] ?? false),
                    (string) ($options['wrap'] ?? ''),
                    (bool) ($options['noConcat'] ?? false),
                    (string) ($options['split'] ?? '|')
                );
            }
        }

        return array_keys($assets);
    }

    /**
     * @return array<array<string|bool>>
     */
    abstract protected function getStylesheetAssetsToLoad(): array;

    /**
     * @return array<mixed>
     */
    public function getJavascriptAssets(string $chartType, PageRenderer $pageRenderer = null): array
    {
        $useAssets = (bool) filter_var($this->getLibraryConfig('assets', 'true'), FILTER_VALIDATE_BOOLEAN);
        if ($useAssets === false) {
            return [];
        }

        $assets = $this->getJavascriptAssetsToLoad();

        // directly include when pageRenderer is given
        if ($pageRenderer instanceof PageRenderer) {
            foreach ($assets as $asset => $options) {
                $pageRenderer->addJsFooterLibrary(
                    md5($asset),
                    $asset,
                    (string) ($options['type'] ?? 'text/javascript'),
                    (bool) ($options['compress'] ?? false),
                    (bool) ($options['forceOnTop'] ?? false),
                    (string) ($options['wrap'] ?? ''),
                    (bool) ($options['noConcat'] ?? false),
                    (string) ($options['split'] ?? '|'),
                    (bool) ($options['async'] ?? false),
                    (string) ($options['integrity'] ?? '')
                );
            }
        }

        return array_keys($assets);
    }

    /**
     *
     * @return array<array<string|bool>>
     */
    abstract protected function getJavascriptAssetsToLoad(): array;

    public function getEntityStylesheet(
        string $chartIdentifier,
        string $chartType,
        ChartData $chartEntity,
        PageRenderer $pageRenderer = null
    ): string {
        return '';
    }

    public function getEntityJavascript(
        string $chartIdentifier,
        string $chartType,
        ChartData $chartEntity,
        PageRenderer $pageRenderer = null
    ): string {
        // check if labels and datasets are not empty ;)
        $labels = $chartEntity->getLabelList();
        $datasets = $chartEntity->getDatasetList();
        if (empty($labels) || empty($datasets)) {
            return '';
        }

        // build datasets for current entity to insert in javascript below
        $datasets = $this->buildEntityDatasetsForJavascript($chartIdentifier, $chartType, $datasets, $chartEntity);

        // create standardized initialization and dataset/labels code
        $initCode = "document.addEventListener('DOMContentLoaded', () => window['Hoogi91.Charts'].init());";
        $initCode .= "window['Hoogi91.chartsData'] = {};";
        /** @psalm-suppress InternalMethod */
        $codeIdentifier = sprintf('chartsData%d', $chartEntity->getUid());
        $code = vsprintf(
            "window['Hoogi91.chartsData']['%s'] = {labels: %s, datasets: %s};",
            [
                $codeIdentifier,
                json_encode($labels, JSON_THROW_ON_ERROR),
                json_encode($datasets, JSON_THROW_ON_ERROR),
            ]
        );

        // directly include when pageRenderer is given
        if ($pageRenderer instanceof PageRenderer) {
            $pageRenderer->addJsFooterInlineCode('chartsInitialization', $initCode);
            $pageRenderer->addJsFooterInlineCode($codeIdentifier, $code);
        }

        return $initCode . $code;
    }

    /**
     * @param array<mixed> $datasets
     *
     * @return array<mixed>
     */
    protected function buildEntityDatasetsForJavascript(
        string $chartIdentifier,
        string $chartType,
        array $datasets,
        ChartData $chartEntity
    ): array {
        $labels = $chartEntity->getDatasetsLabelList();

        return array_map(
            static fn (int $key) => ['data' => $datasets[$key], 'label' => $labels[$key] ?? ''],
            array_keys($datasets)
        );
    }
}
