<?php

namespace Hoogi91\Charts\Form\Types;

class DoughnutChart extends AbstractChartType
{

    public static function getIdentifier(): string
    {
        return 'chart_doughnut';
    }

    public static function getIconIdentifier(): string
    {
        return 'tx_charts_doughnut_chart';
    }
}
