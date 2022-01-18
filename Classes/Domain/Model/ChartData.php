<?php

namespace Hoogi91\Charts\Domain\Model;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

abstract class ChartData extends AbstractEntity
{
    public const TYPE_PLAIN = 0;
    public const TYPE_SPREADSHEET = 1;

    protected string $title;

    protected int $type;

    protected string $labels;

    protected string $datasets;

    protected string $datasetsLabels;

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getType(): int
    {
        return in_array($this->type, $this->getAllowedTypes(), true) ? $this->type : self::TYPE_PLAIN;
    }

    public function setType(int $type = self::TYPE_PLAIN): void
    {
        if (in_array($type, $this->getAllowedTypes(), true)) {
            $this->type = $type;
        }
    }

    public function getLabels(): array
    {
        // only get first row of labels and ignore multiple column/row selections
        $labels = $this->extractLabelList($this->labels);
        return array_shift($labels) ?? [];
    }

    public function setLabels(string $labels): void
    {
        $this->labels = $labels;
    }

    public function getDatasets(): array
    {
        return $this->extractDatasetList($this->datasets);
    }

    public function setDatasets(string $datasets): void
    {
        $this->datasets = $datasets;
    }

    public function getDatasetsLabels(): array
    {
        // only get single row of labels => in javascript this should be mapped together with datasets
        $labels = $this->extractLabelList($this->datasetsLabels);
        return array_shift($labels) ?? [];
    }

    public function setDatasetsLabels(string $datasetsLabels): void
    {
        $this->datasetsLabels = $datasetsLabels;
    }

    protected function getAllowedTypes(): array
    {
        $allowedTypes = [self::TYPE_PLAIN];
        if (ExtensionManagementUtility::isLoaded('spreadsheets')) {
            // only allow spreadsheet type if required extension is loaded
            $allowedTypes[] = self::TYPE_SPREADSHEET;
        }
        return $allowedTypes;
    }

    abstract protected function extractLabelList(string $labelData): array;

    abstract protected function extractDatasetList(string $datasetData): array;
}
