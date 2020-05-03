<?php

namespace Hoogi91\Charts\Tests\Unit;

use Hoogi91\Spreadsheets\Service\CellService;
use Hoogi91\Spreadsheets\Service\ExtractorService;
use Hoogi91\Spreadsheets\Service\RangeService;
use Hoogi91\Spreadsheets\Service\ReaderService;
use Hoogi91\Spreadsheets\Service\SpanService;
use Hoogi91\Spreadsheets\Service\StyleService;
use Hoogi91\Spreadsheets\Service\ValueMappingService;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Trait LegacyTrait
 * @package Hoogi91\Charts\Tests\Unit
 */
trait LegacyTrait
{

    /**
     * Check if spreadsheet is at least given version e.g. 2.0
     * @param string $version
     * @return bool
     */
    protected function isAtLeastTYPO3Version(string $version): bool
    {
        $spreadsheetVersion = ExtensionManagementUtility::getExtensionVersion('core');
        if (!empty($spreadsheetVersion) && version_compare($spreadsheetVersion, $version, '<')) {
            return false;
        }
        return true;
    }

    /**
     * @param string $fixtureFile
     * @param string|callable $range
     * @return callable
     * @throws Exception
     */
    private function getDataCallbackForFixture(string $fixtureFile, $range): callable
    {
        $spreadsheet = (new Xlsx())->load(dirname(__DIR__) . '/Fixtures/' . $fixtureFile);

        // return callback for legacy code
        if ($this->isAtLeastTYPO3Version('10.4') === false) {
            $extractorService = new ExtractorService($spreadsheet);
            return static function ($data) use ($extractorService, $range) {
                if (is_callable($range)) {
                    $range = $range($data);
                }
                return $extractorService->rangeToCellArray($range, false, true, false);
            };
        }

        // create callback for new versions
        $readerService = $this->createConfiguredMock(ReaderService::class, ['getSpreadsheet' => $spreadsheet]);
        $extractorService = new ExtractorService(
            $readerService,
            new CellService(new StyleService(new ValueMappingService())),
            new SpanService(),
            new RangeService(),
            new ValueMappingService()
        );
        return static function ($data) use ($extractorService, $spreadsheet, $range) {
            if (is_callable($range)) {
                $range = $range($data);
            }
            return $extractorService->rangeToCellArray($spreadsheet->getActiveSheet(), $range);
        };
    }
}
