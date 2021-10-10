<?php

namespace Hoogi91\Charts\Controller\Wizard;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TextTableElement
 * @package Hoogi91\Charts\Controller\Wizard
 */
class TextTableElement extends \TYPO3\CMS\Backend\Form\Element\TextTableElement
{

    /**
     * Fixes existing fields which saved data as XML content
     *
     * @return array
     * @todo this fix should be removed in further versions
     */
    public function render(): array
    {
        $result = parent::render();

        preg_match('/<textarea.*>(.*?)<\/textarea>/s', $result['html'], $match);
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
            $result['html']
        );
        return $result;
    }
}
