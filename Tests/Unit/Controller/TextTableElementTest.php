<?php

namespace Hoogi91\Charts\Tests\Unit\Controller;

use Hoogi91\Charts\Controller\Wizard\TextTableElement;
use Hoogi91\Charts\Tests\Unit\CacheTrait;
use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class TextTableElementTest
 * @package Hoogi91\Charts\Tests\Unit\Controller
 */
class TextTableElementTest extends UnitTestCase
{

    use CacheTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpCaches();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->resetPackageManager();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testConfigurationFix(string $formValue, string $expected): void
    {
        GeneralUtility::addInstance(IconFactory::class, $this->createMock(IconFactory::class));

        $nodeFactory = $this->createConfiguredMock(
            NodeFactory::class,
            ['create' => $this->createConfiguredMock(AbstractNode::class, ['render' => []])]
        );
        $element = new TextTableElement($nodeFactory, [
            'parameterArray' => [
                'itemFormElValue' => $formValue,
                'fieldConf' => ['config' => []],
                'fieldChangeFunc' => [],
            ],
        ]);

        preg_match('/<textarea.*>(.*?)<\/textarea>/s', $element->render()['html'], $match);
        self::assertTrue(isset($match[1]));
        self::assertSame($expected, $match[1]);
    }

    public function dataProvider(): array
    {
        return [
            'with empty value' => [
                'formValue' => '',
                'expected' => ''
            ],
            'with TYPO3 table format' => [
                'formValue' => '|Germany|Europe|America|China|',
                'expected' => '|Germany|Europe|America|China|'
            ],
            'with XML table format' => [
                'formValue' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
                    <T3TableWizard>
                        <numIndex index="2" type="array">
                            <numIndex index="2">Germany</numIndex>
                            <numIndex index="4">Europe</numIndex>
                            <numIndex index="6">America</numIndex>
                            <numIndex index="8">China</numIndex>
                        </numIndex>
                    </T3TableWizard>',
                'expected' => '|Germany|Europe|America|China|',
            ],
        ];
    }
}
