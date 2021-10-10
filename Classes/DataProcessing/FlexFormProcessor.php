<?php

namespace Hoogi91\Charts\DataProcessing;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * This data processor can be used for processing data
 * for the content elements which have flexform contents in one field
 *
 * Example TypoScript configuration:
 * 10 = Pixelant\ThemeCore\DataProcessing\FlexFormProcessor
 * 10 {
 *   if.isTrue.field = pi_flexform
 *   fieldName = pi_flexform
 *   as = flexform
 * }
 *
 * whereas "flexform" can be used as a variable {flexform} inside Fluid to fetch values.
 * if as = settings, flexform settings are merged with contentObjectConfiguration['settings.']
 *
 * Class FlexFormProcessor
 * @package Hoogi91\Charts\DataProcessing
 */
class FlexFormProcessor implements DataProcessorInterface
{

    /**
     * Process flexform field data to an array
     *
     * @param ContentObjectRenderer $cObj The data of the content element or page
     * @param array $contentObjectConfiguration The configuration of Content Object
     * @param array $processorConfiguration The configuration of this processor
     * @param array $processedData Key/value store of processed data
     *                                                          (e.g. to be passed to a Fluid View)
     *
     * @return array the processed data as key/value store
     */
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
        /** @var \TYPO3\CMS\Core\Service\FlexFormService $flexFormService */
        $flexFormService = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Service\FlexFormService::class);
        $processedData[$targetVariableName] = $flexFormService->convertFlexFormContentToArray($cObj->data[$fieldName]);

        // if target variable is settings, try to merge it with contentObjectConfiguration['settings.']
        if ($targetVariableName === 'settings') {
            if (is_array($contentObjectConfiguration['settings.'])) {
                $convertedConf = GeneralUtility::removeDotsFromTS($contentObjectConfiguration['settings.']);
                foreach ($convertedConf as $key => $value) {
                    if (!isset($processedData['settings'][$key]) || $processedData['settings'][$key] === false) {
                        $processedData['settings'][$key] = $value;
                    }
                }
            }
        }

        return $processedData;
    }
}
