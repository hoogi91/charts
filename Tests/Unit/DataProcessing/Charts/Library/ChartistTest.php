<?php

namespace Hoogi91\Charts\Tests\Unit\DataProcessing\Charts\Library;

use Hoogi91\Charts\DataProcessing\Charts\Library\Chartist;
use Hoogi91\Charts\Domain\Model\ChartDataSpreadsheet;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Class ChartistTest
 * @package Hoogi91\Charts\Tests\Unit\DataProcessing\Charts\Library
 */
class ChartistTest extends UnitTestCase
{

    /**
     * @var Chartist
     */
    protected $library;

    /**
     * @var ChartDataSpreadsheet|MockObject
     */
    protected $chartDataSpreadsheetModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->library = new Chartist();
        $this->chartDataSpreadsheetModel = $this->createConfiguredMock(
            ChartDataSpreadsheet::class,
            [
                'getUid' => 123456,
                'getLabels' => ['Label 1', 'Label 2', 'Label 3'],
                'getDatasets' => [
                    ['Data 1-1', 'Data 1-2', 'Data 1-3'],
                    ['Data 2-1', 'Data 2-2', 'Data 2-3'],
                    ['Data 3-1', 'Data 3-2', 'Data 3-3'],
                ]
            ]
        );
    }

    public function testProperReturnTypes(): void
    {
        $this->assertEquals(Chartist::NAME, $this->library->getName());
        $this->assertIsArray($this->library->getDefaultColors());
        $this->assertIsArray($this->library->getDataStructures());
    }

    public function testStylesheetAssetBuilding(): void
    {
        // chartist defines at least one css library to add
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::atLeastOnce())->method('addCssLibrary');

        $stylesheets = $this->library->getStylesheetAssets('bar', $pageRenderer);
        $this->assertNotEmpty($stylesheets);
        $this->assertIsArray($stylesheets);
    }

    public function testJavascriptAssetBuilding(): void
    {
        // chartist defines at least one js library to add
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::atLeastOnce())->method('addJsFooterLibrary');

        $javascripts = $this->library->getJavascriptAssets('line', $pageRenderer);
        $this->assertIsArray($javascripts);
        $this->assertCount(2, $javascripts);
    }

    public function testStylesheetEntityBuilding(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::once())->method('addCssInlineBlock')->with(
            'test-identifier-123',
            self::isType('string'),
            true,
            true
        );

        $stylesheet = $this->library->getEntityStylesheet(
            'test-identifier-123',
            'pie',
            $this->chartDataSpreadsheetModel,
            $pageRenderer
        );
        $this->assertNotEmpty($stylesheet);
        $this->assertIsString($stylesheet);
    }

    public function testJavascriptEntityBuilding(): void
    {
        $pageRenderer = $this->createMock(PageRenderer::class);
        $pageRenderer->expects(self::exactly(2))
            ->method('addJsFooterInlineCode')
            ->withConsecutive(
                ['chartsInitialization', self::isType('string')],
                ['chartsData123456', self::isType('string')]
            );

        $javascript = $this->library->getEntityJavascript(
            'test-identifier-123',
            'doughnut',
            $this->chartDataSpreadsheetModel,
            $pageRenderer
        );
        $this->assertNotEmpty($javascript);
        $this->assertIsString($javascript);
    }
}
