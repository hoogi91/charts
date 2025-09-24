<?php

declare(strict_types=1);

namespace Hoogi91\Charts\Form\Types;

abstract class AbstractChartType implements ChartTypeInterface
{
    /**
     * @param array<mixed> $columnOverrides
     */
    public static function register(array $columnOverrides = []): void
    {
        $identifier = static::getIdentifier();
        $iconIdentifier = static::getIconIdentifier();
        
        $GLOBALS['TCA']['tt_content']['ctrl']['typeicon_classes'][$identifier] = $iconIdentifier;
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

    abstract public static function getIdentifier(): string;

    abstract public static function getIconIdentifier(): string;

    private static function getDefaultShowItems(bool $addFlexFormField = false): string
    {
        $coreXlf = 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf';
        $frontendXlf = 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf';

        return '--div--;' . $coreXlf . ':general,
            --palette--;' . $frontendXlf . ':palette.general;general,
            --palette--;' . $frontendXlf . ':palette.headers;headers,
            tx_charts_chartdata,
            ' . ($addFlexFormField ? 'pi_flexform,' : '') . '
        --div--;' . $frontendXlf . ':tabs.appearance,
            --palette--;' . $frontendXlf . ':palette.frames;frames,
            --palette--;' . $frontendXlf . ':palette.appearanceLinks;appearanceLinks,
        --div--;' . $coreXlf . ':language,
            --palette--;;language,
        --div--;' . $coreXlf . ':access,
            --palette--;;hidden,
            --palette--;' . $frontendXlf . ':palette.access;access,
        --div--;' . $coreXlf . ':categories,
        --div--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_category.tabs.category,categories,
        --div--;' . $coreXlf . ':notes,rowDescription,
        --div--;' . $coreXlf . ':extended';
    }
}
