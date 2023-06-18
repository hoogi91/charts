<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Tests\Unit\Form;

use Hoogi91\Charts\Form\Element\ColorPaletteInputElement;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Backend\Form\AbstractNode;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

use const PHP_EOL;

class ColorPaletteInputElementTest extends UnitTestCase
{
    /** @var NodeFactory&MockObject */
    private MockObject $nodeFactory;

    public function setUp(): void
    {
        GeneralUtility::addInstance(IconFactory::class, $this->createMock(IconFactory::class));
        $languageService = $this->createMock(LanguageService::class);
        $languageService->method('sL')->willReturnMap(
            [
                ['LLL:EXT:charts/Resources/Private/Language/locallang_db.xlf:color_palette.empty', 'Empty'],
                ['LLL:EXT:charts/Resources/Private/Language/locallang_db.xlf:color_palette.newButton', 'New Button'],
            ]
        );
        $GLOBALS['LANG'] = $languageService;

        $this->nodeFactory = $this->createMock(NodeFactory::class);
        $this->nodeFactory->method('create')->willReturn(
            $this->createConfiguredMock(AbstractNode::class, ['render' => ['html' => '[rendered] field information']])
        );
    }

    /**
     * @dataProvider dataProvider
     *
     * @param array<mixed> $data
     * @param array<mixed> $modules
     */
    public function testRender(string $expected, array $data, array $modules): void
    {
        $rendered = (new ColorPaletteInputElement($this->nodeFactory, $data))->render();
        self::assertEquals($modules, $rendered['requireJsModules']);
        if (is_file($expected)) {
            self::assertStringEqualsFile($expected, $rendered['html'] . PHP_EOL); // files always have an ending newline
        } else {
            self::assertSame($expected, $rendered['html']);
        }
    }

    /**
     * @return array<mixed>
     */
    public function dataProvider(): array
    {
        return [
            'empty data' => [
                'expected' => '<div class="alert alert-danger">' .
                    'Input form name not set. Please inform administrator!</div>',
                'data' => [],
                'modules' => [],
            ],
            'input containing html' => [
                'expected' => __DIR__ . '/ColorPaletteInputElement.html',
                'data' => [
                    'parameterArray' => [
                        'itemFormElName' => 'some-name',
                        'itemFormElValue' => 'some-value <tag>123</tag>',
                    ],
                ],
                'modules' => [JavaScriptModuleInstruction::forRequireJS('TYPO3/CMS/Charts/ColorPaletteInputElement')],
            ],
        ];
    }
}
