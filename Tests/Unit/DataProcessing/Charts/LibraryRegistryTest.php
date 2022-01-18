<?php

namespace Hoogi91\Charts\Tests\Unit\DataProcessing\Charts;

use Hoogi91\Charts\DataProcessing\Charts\Library\Chartist;
use Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs;
use Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry;
use Hoogi91\Charts\RegisterChartLibraryException;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class LibraryRegistryTest
 * @package Hoogi91\Charts\Tests\Unit\DataProcessing\Charts
 */
class LibraryRegistryTest extends UnitTestCase
{

    private LibraryRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new LibraryRegistry();
    }

    public function testLibraryRegistration(): void
    {
        $this->registry->register('chartist', Chartist::class);
        $this->registry->register('chartist123', Chartist::class);
        $this->assertInstanceOf(Chartist::class, $this->registry->getLibrary('chartist'));
        $this->assertInstanceOf(Chartist::class, $this->registry->getLibrary('chartist123'));
    }

    public function testLibrarySelectGenerator(): void
    {
        $this->registry->register('chartist', Chartist::class);
        $this->registry->register('chart.js', ChartJs::class);

        $select = $this->registry->getLibrarySelect(
            [
                'fieldName' => 'html-fieldname',
                'fieldValue' => 'chart.js',
            ]
        );

        $this->assertNotEmpty($select);
        $this->assertIsString($select);
        $this->assertStringContainsString(
            '<input type="hidden" name="html-fieldname" value="chart.js"/>',
            $select
        );
        $this->assertStringContainsString(
            '<option value="chart.js" selected="selected">chart.js (' . ChartJs::class . ')</option>',
            $select
        );
        $this->assertStringContainsString(
            '<option value="chartist">chartist (' . Chartist::class . ')</option>',
            $select
        );
    }
}
