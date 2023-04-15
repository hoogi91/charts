<?php

declare(strict_types=1);

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

    /**
     * @param array<bool|float|int|string|null> $arguments
     */
    protected function renderModuleUrl(array $arguments = []): string
    {
        $editParamName = sprintf('edit[%s][%d]', $arguments['recordTable'], $arguments['recordId']);
        $returnUrl = isset($arguments['returnPid'])
            ? $this->getModuleUrl('web_layout', ['id' => $arguments['returnPid']])
            : GeneralUtility::getIndpEnv('REQUEST_URI');

        return $this->getModuleUrl('record_edit', [
            $editParamName => 'edit',
            'returnUrl' => $returnUrl,
        ]);
    }

    /**
     * @param array<mixed> $params
     */
    private function getModuleUrl(string $module, array $params): string
    {
        return (string)GeneralUtility::makeInstance(UriBuilder::class)->buildUriFromRoute($module, $params);
    }
}
