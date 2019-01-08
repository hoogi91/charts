<?php

namespace Hoogi91\Charts\Form\Types;

/**
 * Class Chart
 * @package Hoogi91\Charts\Form\Types
 */
class Chart
{
    const TYPE_BAR = 'chart_bar';
    const TYPE_LINE = 'chart_line';
    const TYPE_PIE = 'chart_pie';
    const TYPE_DOUGHNUT = 'chart_doughnut';
    const LANGUAGE_FILE = 'LLL:EXT:charts/Resources/Private/Language/locallang_db.xlf';

    /**
     * @return array
     */
    public static function getTypes()
    {
        return [self::TYPE_BAR, self::TYPE_LINE, self::TYPE_PIE, self::TYPE_DOUGHNUT];
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public static function getShortName($type = self::TYPE_BAR)
    {
        switch ($type) {
            case self::TYPE_BAR:
                return 'bar';
            case self::TYPE_LINE:
                return 'line';
            case self::TYPE_PIE:
                return 'pie';
            case self::TYPE_DOUGHNUT:
                return 'doughnut';
        }
        return '';
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public static function getIconIdentifier($type = self::TYPE_BAR)
    {
        switch ($type) {
            case self::TYPE_BAR:
                return 'tx_charts_bar_chart';
            case self::TYPE_LINE:
                return 'tx_charts_line_chart';
            case self::TYPE_PIE:
                return 'tx_charts_pie_chart';
            case self::TYPE_DOUGHNUT:
                return 'tx_charts_doughnut_chart';
        }
        return 'tx_charts_chart';
    }

    /**
     * @param bool $includeDividerBefore
     */
    public static function addCTypeSelectItems($includeDividerBefore = true)
    {
        if ($includeDividerBefore === true) {
            $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = [
                sprintf('%s:tt_content.CType.div._charts_', static::LANGUAGE_FILE),
                '--div--',
            ];
        };

        foreach (static::getTypes() as $type) {
            $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = [
                sprintf('%s:tt_content.CType.%s', static::LANGUAGE_FILE, $type),
                $type,
                static::getIconIdentifier($type),
            ];
        }
    }

    /**
     * @param string $type
     * @param array  $columnOverrides
     */
    public static function addTypeConfiguration($type = self::TYPE_BAR, $columnOverrides = [])
    {
        if (empty($type)) {
            return;
        }

        // check if current type has flexform configuration
        $addFlexFormField = array_key_exists(
            '*,' . $type,
            $GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds']
        );

        $typeConfig = [
            'showitem' => static::getDefaultShowItems($addFlexFormField),
        ];

        if (!empty($columnOverrides)) {
            $typeConfig['columnsOverrides'] = $columnOverrides;
        }

        if (!isset($GLOBALS['TCA']['tt_content']['types'][$type])) {
            $GLOBALS['TCA']['tt_content']['types'][$type] = [];
        }
        $GLOBALS['TCA']['tt_content']['types'][$type] += $typeConfig;
    }

    /**
     * @param bool $addFlexFormField
     *
     * @return string
     * @phpcs:disable
     */
    public static function getDefaultShowItems($addFlexFormField = false)
    {
        return '--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.headers;headers,
            tx_charts_chartdata,
            ' . ($addFlexFormField ? 'pi_flexform,' : '') . '
        --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
            --palette--;;language,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
            --palette--;;hidden,
            --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
        --div--;LLL:EXT:lang/Resources/Private/Language/locallang_tca.xlf:sys_category.tabs.category,categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended';
    }
}
