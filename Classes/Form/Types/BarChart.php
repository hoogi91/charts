<?php

namespace Hoogi91\Charts\Form\Types;

class BarChart extends AbstractChartType
{

    public static function getIdentifier(): string
    {
        return 'chart_bar';
    }

    public static function getIconIdentifier(): string
    {
        return 'tx_charts_bar_chart';
    }
}
