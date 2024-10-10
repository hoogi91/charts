<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;

use const ENT_QUOTES;

class ColorPaletteInputElement extends AbstractFormElement
{
    /**
     * @var array<array<string>>
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $defaultFieldInformation = [
        'tcaDescription' => [
            'renderType' => 'tcaDescription',
        ],
    ];

    /**
     * @return array<mixed>
     */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $inputName = (string)($this->data['parameterArray']['itemFormElName'] ?? '');
        if (empty($inputName)) {
            $resultArray['html'] = '<div class="alert alert-danger">';
            $resultArray['html'] .= 'Input form name not set. Please inform administrator!</div>';
            return $resultArray;
        }

        $inputValue = htmlspecialchars($this->data['parameterArray']['itemFormElValue'] ?? '', ENT_QUOTES);
        $inputIdentifier = md5($inputName);

        $emptyPaletteContent = $this->getLanguageService()->sL(
            'LLL:EXT:charts/Resources/Private/Language/locallang_db.xlf:color_palette.empty'
        );
        $newButtonText = $this->getLanguageService()->sL(
            'LLL:EXT:charts/Resources/Private/Language/locallang_db.xlf:color_palette.newButton'
        );

        // Use PageRenderer to include the JavaScript module
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadJavaScriptModule('@hoogi91/charts/ColorPaletteInputElement.js');

        $resultArray['html'] = <<<HTML
<div class="formengine-field-item t3js-formengine-field-item">
    {$this->renderFieldInformation()['html']}
    <div class="form-control-wrap">
        <div class="form-wizards-wrap">
            <input id="$inputIdentifier" type="hidden" name="$inputName" value="$inputValue"/>
            <color-palette ref="$inputIdentifier" mode="preview" style="display:block;height:32px">
                <span slot="empty">$emptyPaletteContent</span>
                <span slot="newButtonIcon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16">
                        <g class="icon-color">
                            <path d="M11.5 10.6c-.2.1-.4.1-.6.1-1.8 0-4.4-6.2-4.4-8.3 
                            0-.8.2-1 .4-1.2-2.1.3-4.7 1.1-5.5 2.1-.2.2-.3.6-.3 1.1C1.1 
                            7.7 4.5 15 7 15c1.1 0 3-1.8 4.5-4.4M10.4 1c2.2 0 4.5.4 4.5 
                            1.6 0 2.6-1.6 5.7-2.5 5.7-1.5 0-3.3-4.1-3.3-6.2 0-.9.4-1.1 1.3-1.1"/>
                        </g>
                    </svg>
                </span>
                <span slot="newButtonText">$newButtonText</span>
            </color-palette>
        </div>
    </div>
</div>
HTML;

        return $resultArray;
    }
}
