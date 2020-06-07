<?php

namespace Hoogi91\Charts\ViewHelpers\Backend;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ViewHelper to create a link to edit a record of a specific table
 *
 * Class EditLinkViewHelper
 * @package Hoogi91\Charts\ViewHelpers\Backend
 */
class EditLinkViewHelper extends AbstractLinkViewHelper
{

    /**
     * Initializes the arguments
     */
    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerTagAttribute('target', 'string', 'Target of link', false);
        $this->registerTagAttribute('returnPid', 'int', 'pid of the record to edit for returnUrl', false);
    }

    /**
     * @param array $arguments
     *
     * @return string
     */
    protected function renderModuleUrl(array $arguments = []): string
    {
        if (isset($arguments['returnPid'])) {
            $returnUrl = $this->getModuleUrl('web_layout', ['id' => $arguments['returnPid']]);
        } else {
            $returnUrl = GeneralUtility::getIndpEnv('REQUEST_URI');
        }

        $editParamName = sprintf('edit[%s][%d]', $arguments['recordTable'], $arguments['recordId']);
        $urlParameters = [
            $editParamName => 'edit',
            'returnUrl' => $returnUrl,
        ];

        return $this->getModuleUrl('record_edit', $urlParameters);
    }

    /**
     * @param string $module
     * @param array $params
     * @return string
     */
    private function getModuleUrl(string $module, array $params): string
    {
        // @phpstan-ignore-next-line
        if (version_compare(TYPO3_version, '10.0', '<')) {
            /** @deprecated since v1.1.0 and will be removed in v2.0 */
            // @phpstan-ignore-next-line
            return \TYPO3\CMS\Backend\Utility\BackendUtility::getModuleUrl($module, $params);
        }

        /** @var \TYPO3\CMS\Backend\Routing\UriBuilder $uriBuilder */
        $uriBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Backend\Routing\UriBuilder::class);
        return (string)$uriBuilder->buildUriFromRoute($module, $params);
    }
}
