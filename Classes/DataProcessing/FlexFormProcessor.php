<?php

namespace Hoogi91\Charts\DataProcessing;

use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

class FlexFormProcessor implements DataProcessorInterface
{

    private FlexFormService $flexFormService;

    public function __construct(FlexFormService $flexFormService)
    {
        $this->flexFormService = $flexFormService;
    }

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        if (isset($processorConfiguration['if.']) && !$cObj->checkIf($processorConfiguration['if.'])) {
            return $processedData;
        }

        // set target variable, default "flexform"
        $targetVariableName = $cObj->stdWrapValue('as', $processorConfiguration, 'flexform');

        // set fieldname, default "pi_flexform"
        $fieldName = $cObj->stdWrapValue('fieldName', $processorConfiguration, 'pi_flexform');

        // parse flexform
        $processedData[$targetVariableName] = $this->flexFormService->convertFlexFormContentToArray(
            $cObj->data[$fieldName]
        );

        // if target variable is settings, try to merge it with contentObjectConfiguration['settings.']
        if ($targetVariableName === 'settings' && is_array($contentObjectConfiguration['settings.'])) {
            $convertedConf = GeneralUtility::removeDotsFromTS($contentObjectConfiguration['settings.']);
            foreach ($convertedConf as $key => $value) {
                if (!isset($processedData['settings'][$key]) || $processedData['settings'][$key] === false) {
                    $processedData['settings'][$key] = $value;
                }
            }
        }

        return $processedData;
    }
}
