<?php

namespace Hoogi91\Charts\Form\Types;

class LineChart extends AbstractChartType
{

    public static function getIdentifier(): string
    {
        return 'chart_line';
    }

    public static function getIconIdentifier(): string
    {
        return 'tx_charts_line_chart';
    }
}
