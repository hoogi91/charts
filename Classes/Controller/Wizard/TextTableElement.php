<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Controller\Wizard;

use TYPO3\CMS\Backend\Form\Element\TextTableElement as Typo3TextTableElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TextTableElement extends Typo3TextTableElement
{
    /**
     * Fixes existing fields which saved data as XML content
     *
     * @return array<mixed>
     * @todo this fix should be removed in further versions
     */
    public function render(): array
    {
        /** @psalm-suppress InternalMethod */
        $result = parent::render();

        preg_match('/<textarea.*>(.*?)<\/textarea>/s', (string) $result['html'], $match);
        if (!isset($match[1]) || empty($match[1])) {
            return $result;
        }

        $data = GeneralUtility::xml2array(html_entity_decode($match[1]));
        if (!is_array($data)) {
            return $result;
        }

        foreach ($data as $k => $subData) {
            $data[$k] = '|' . implode('|', $subData) . '|';
        }

        $result['html'] = preg_replace(
            '/(<textarea.*>)(.*?)(<\/textarea>)/s',
            '$1' . implode("\n", $data) . '$3',
            (string) $result['html']
        );

        return $result;
    }
}
