<?php

namespace Hoogi91\Charts\ViewHelpers\Backend;

use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class EditLinkViewHelper extends AbstractLinkViewHelper
{

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerTagAttribute('target', 'string', 'Target of link', false);
        $this->registerTagAttribute('returnPid', 'int', 'pid of the record to edit for returnUrl', false);
    }

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

    private function getModuleUrl(string $module, array $params): string
    {
        return (string)GeneralUtility::makeInstance(UriBuilder::class)->buildUriFromRoute($module, $params);
    }
}
