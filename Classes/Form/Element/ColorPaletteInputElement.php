<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;

/**
 * Class ColorPaletteInputElement
 * @package Hoogi91\Charts\Form\Element
 */
class ColorPaletteInputElement extends AbstractFormElement
{

    /**
     * Default field information enabled for this element.
     *
     * @var array
     */
    protected $defaultFieldInformation = [
        'tcaDescription' => [
            'renderType' => 'tcaDescription',
        ],
    ];

    /**
     * Renders input field to select multiple colors with picker to create a palette
     *
     * @return array
     */
    public function render(): array
    {
        $inputName = $this->data['parameterArray']['itemFormElName'] ?? null;
        $inputValue = htmlspecialchars($this->data['parameterArray']['itemFormElValue'] ?? '', ENT_QUOTES);
        $inputIdentifier = md5($inputName);

        $resultArray = $this->initializeResultArray();
        $resultArray['requireJsModules'] = ['TYPO3/CMS/Charts/ColorPaletteInputElement'];
        $resultArray['html'] = <<<HTML
<div class="formengine-field-item t3js-formengine-field-item">
    {$this->renderFieldInformation()['html']}
    <div class="form-control-wrap">
        <div class="form-wizards-wrap">
            <input id="$inputIdentifier" type="hidden" name="$inputName" value="$inputValue"/>
            <color-palette ref="$inputIdentifier" mode="preview" style="height:32px"></color-palette>
        </div>
    </div>
</div>
HTML;

        return $resultArray;
    }
}
