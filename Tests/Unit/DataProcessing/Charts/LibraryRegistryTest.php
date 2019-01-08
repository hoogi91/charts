<?php

namespace Hoogi91\Charts\Tests\DataProcessing\Charts;

use Hoogi91\Charts\ChartException;
use Hoogi91\Charts\DataProcessing\Charts\Library\Chartist;
use Hoogi91\Charts\DataProcessing\Charts\Library\ChartJs;
use Hoogi91\Charts\DataProcessing\Charts\LibraryRegistry;
use Hoogi91\Charts\RegisterChartLibraryException;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Cache\Backend\NullBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class LibraryRegistryTest
 * @package Hoogi91\Charts\Tests\DataProcessing\Charts
 */
class LibraryRegistryTest extends UnitTestCase
{
    /**
     * @var LibraryRegistry
     */
    protected $registry;

    protected function setUp()
    {
        parent::setUp();

        // disable extbase object caching to let object manager work in unit tests
        /** @var CacheManager $cacheManager */
        $cacheManager = GeneralUtility::makeInstance(CacheManager::class);
        $cacheManager->setCacheConfigurations([
            'extbase_object' => [
                'backend'  => NullBackend::class,
                'frontend' => VariableFrontend::class,
            ],
        ]);

        $this->registry = new LibraryRegistry();
    }

    /**
     * @test
     */
    public function testLibraryRegistration()
    {
        $this->registry->register('chartist', Chartist::class);
        $this->registry->register('chartist123', Chartist::class);
        $this->assertInstanceOf(Chartist::class, $this->registry->getLibrary('chartist'));
        $this->assertInstanceOf(Chartist::class, $this->registry->getLibrary('chartist123'));
    }

    /**
     * @test
     */
    public function testLibraryRegistrationWithInvalidClass()
    {
        $this->expectException(RegisterChartLibraryException::class);
        $this->registry->register('chartist', ChartException::class);
    }

    /**
     * @test
     */
    public function testLibraryRegistrationOverride()
    {
        // check if exception is thrown when override is not explicit
        $this->expectException(RegisterChartLibraryException::class);
        $this->registry->register('chartist', Chartist::class);
        $this->registry->register('chartist', Chartist::class);

        // check if forcing override works
        $this->registry->register('chart.js', Chartist::class);
        $this->registry->register('chart.js', ChartJs::class, true);
        $this->assertInstanceOf(ChartJs::class, $this->registry->getLibrary('chart.js'));
    }

    /**
     * @test
     */
    public function testUnknownLibraryGetter()
    {
        $this->assertNull($this->registry->getLibrary('loremIpsum'));
    }

    /**
     * @test
     */
    public function testLibrarySelectGenerator()
    {
        $this->registry->register('chartist', Chartist::class);
        $this->registry->register('chart.js', ChartJs::class);

        $select = $this->registry->getLibrarySelect([
            'fieldName'  => 'html-fieldname',
            'fieldValue' => 'chart.js',
        ]);

        $this->assertNotEmpty($select);
        $this->assertInternalType('string', $select);
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
