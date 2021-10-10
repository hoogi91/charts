<?php

namespace Hoogi91\Charts\Form\Types;

abstract class AbstractChartType implements ChartTypeInterface
{

    /**
     * Register chart type
     */
    public static function register(array $columnOverrides = []): void
    {
        $identifier = static::getIdentifier();
        $iconIdentifier = static::getIconIdentifier();

        $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['items'][] = [
            sprintf('%s:tt_content.CType.%s', self::LANGUAGE_FILE, $identifier),
            $identifier,
            $iconIdentifier,
        ];

        // check if current type has flexform configuration
        $addFlexFormField = array_key_exists(
            '*,' . $identifier,
            $GLOBALS['TCA']['tt_content']['columns']['pi_flexform']['config']['ds'] ?? []
        );

        $typeConfig = [
            'showitem' => self::getDefaultShowItems($addFlexFormField),
        ];
        if (!empty($columnOverrides)) {
            $typeConfig['columnsOverrides'] = $columnOverrides;
        }

        if (!isset($GLOBALS['TCA']['tt_content']['types'][$identifier])) {
            $GLOBALS['TCA']['tt_content']['types'][$identifier] = [];
        }
        $GLOBALS['TCA']['tt_content']['types'][$identifier] += $typeConfig;
    }

    /**
     * @return string
     */
    abstract public static function getIdentifier(): string;

    /**
     * @return string
     */
    abstract public static function getIconIdentifier(): string;

    /**
     * @param bool $addFlexFormField
     *
     * @return string
     */
    private static function getDefaultShowItems(bool $addFlexFormField = false): string
    {
        // phpcs:disable
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
        --div--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_category.tabs.category,categories,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,rowDescription,
        --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended';
    }
    // phpcs:enable
}
