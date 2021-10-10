<?php

namespace Hoogi91\Charts\Tests\Unit\DataProcessing\Charts;

use Hoogi91\Charts\DataProcessing\Charts\Library\Chartist;
use Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs;
use Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry;
use Hoogi91\Charts\RegisterChartLibraryException;
use Hoogi91\Charts\Tests\Unit\CacheTrait;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class LibraryRegistryTest
 * @package Hoogi91\Charts\Tests\Unit\DataProcessing\Charts
 */
class LibraryRegistryTest extends UnitTestCase
{

    /**
     * @var LibraryRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

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

    public function testLibraryRegistrationWithInvalidClass(): void
    {
        $this->expectException(RegisterChartLibraryException::class);
        $this->registry->register('chartist', '\Vendor\Unknown\ClassName');
    }

    public function testLibraryRegistrationWithInvalidLibraryClass(): void
    {
        $this->expectException(RegisterChartLibraryException::class);
        $this->registry->register('chartist', RegisterChartLibraryException::class);
    }

    public function testLibraryRegistrationOverride(): void
    {
        // check if forcing override works
        $this->registry->register('chart.js', Chartist::class);
        $this->registry->register('chart.js', ChartJs::class, true);
        $this->assertInstanceOf(ChartJs::class, $this->registry->getLibrary('chart.js'));

        // check if exception is thrown when override is not explicit
        $this->expectException(RegisterChartLibraryException::class);
        $this->registry->register('chartist', Chartist::class);
        $this->registry->register('chartist', Chartist::class);
    }

    public function testUnknownLibraryGetter(): void
    {
        $this->assertNull($this->registry->getLibrary('loremIpsum'));
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
        $this->assertContains(
            '<input type="hidden" name="html-fieldname" value="chart.js"/>',
            $select
        );
        $this->assertContains(
            '<option value="chart.js" selected="selected">chart.js (' . ChartJs::class . ')</option>',
            $select
        );
        $this->assertContains(
            '<option value="chartist">chartist (' . Chartist::class . ')</option>',
            $select
        );
    }
}
