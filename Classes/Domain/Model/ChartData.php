<?php

namespace Hoogi91\Charts\Domain\Model;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

abstract class ChartData extends AbstractEntity
{
    public const TYPE_PLAIN = 0;
    public const TYPE_SPREADSHEET = 1;

    protected string $title = '';
    protected int $type = self::TYPE_PLAIN;

    protected string $labels = '';
    protected string $datasets = '';
    protected string $datasetsLabels = '';

    // TODO: update these fallback properties when TYPO3 supports array types in data mapper
    // see TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper::thawProperties
    protected string $databaseBackground = '';
    protected string $databaseBorder = '';

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
        return array_column($labels, '0') ?? []; // grab first column of every row :)
    }

    public function setDatasetsLabels(string $datasetsLabels): void
    {
        $this->datasetsLabels = $datasetsLabels;
    }

    public function getBackgroundColors(): array
    {
        return array_values(array_filter(explode('|', $this->databaseBackground)));
    }

    public function setBackgroundColors(array $backgroundColors): void
    {
        $this->databaseBackground = implode('|', array_map('trim', $backgroundColors));
    }

    public function getBorderColors(): array
    {
        return array_values(array_filter(explode('|', $this->databaseBorder)));
    }

    public function setBorderColors(array $borderColors): void
    {
        $this->databaseBorder = implode('|', array_map('trim', $borderColors));
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
