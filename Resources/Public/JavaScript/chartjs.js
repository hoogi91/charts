var Hoogi91 = Hoogi91 || {};
Hoogi91.Charts = {
    chartsData: null,
    chartsContainer: null,

    /**
     * initialize all charts that can be found if available options are filled
     */
    init: function () {
        if (typeof Hoogi91.chartsData === 'object') {
            this.chartsData = Hoogi91.chartsData;
        }

        this.chartsContainer = document.querySelectorAll('.chart-container > .chart');
        if (this.chartsContainer.length > 0 && this.chartsData !== null && Object.keys(this.chartsData).length > 0) {
            for (var i = 0; i < this.chartsContainer.length; ++i) {
                var element = this.chartsContainer[i];

                // check id, type and datakey of current chart element and break if something is not available
                var type = element.getAttribute('data-chart-type') || '';
                var dataKey = element.getAttribute('data-chart-data') || '';
                if (type.length <= 0 || dataKey.length <= 0) {
                    continue;
                }

                var chartData = this.getChartData(dataKey);
                if (chartData.length <= 0) {
                    continue;
                }

                var labels = chartData.labels || [];
                var datasets = chartData.datasets || [];
                if (labels.length > 0 && datasets.length > 0) {
                    switch (type) {
                        case 'chart_bar':
                            this.createBarChart(element, labels, datasets);
                            break;
                        case 'chart_line':
                            this.createLineChart(element, labels, datasets);
                            break;
                        case 'chart_pie':
                            this.createPieChart(element, labels, datasets);
                            break;
                        case 'chart_doughnut':
                            this.createDoughnutChart(element, labels, datasets);
                            break;
                    }
                }
            }
        }
    },

    /**
     * get chart data by key
     *
     * @param key
     * @returns object
     */
    getChartData: function (key) {
        if (typeof this.chartsData[key] === 'undefined') {
            return {};
        }
        return this.chartsData[key];
    },

    /**
     * build bar chart on element with labels and datasets
     *
     * @param element
     * @param labels
     * @param datasets
     */
    createBarChart: function (element, labels, datasets) {
        var _this = this;
        var options = _this.getChartOptions(element);
        return new Chart(element.getContext('2d'), {
            type: _this.getKeyOfObject(options, 'bar.horizontal', 0) === '1' ? 'horizontalBar' : 'bar',
            data: {
                labels: labels,
                datasets: _this.createDatasets(datasets, function (set) {
                    return {
                        label: set['label'],
                        data: set['data'],
                        backgroundColor: typeof set['background'] === 'object' ? set['background'] : [],
                        borderColor: typeof set['border'] === 'object' ? set['border'] : [],
                        borderWidth: 1
                    }
                })
            },
            options: {
                legend: {
                    display: _this.getKeyOfObject(options, 'legend.active', 0) === '1',
                    position: _this.getKeyOfObject(options, 'legend.position', 'top')
                },
                scales: {
                    xAxes: [{
                        stacked: _this.getKeyOfObject(options, 'bar.stacked', 0) === '1',
                        ticks: _this.getTicksConfig(options, 'x'),
                        scaleLabel: _this.getAxisLabelConfig(options, 'x'),
                    }],
                    yAxes: [{
                        stacked: _this.getKeyOfObject(options, 'bar.stacked', 0) === '1',
                        ticks: _this.getTicksConfig(options, 'y'),
                        scaleLabel: _this.getAxisLabelConfig(options, 'y'),
                    }]
                }
            }
        });
    },

    /**
     * build line chart on element with labels and datasets
     *
     * @param element
     * @param labels
     * @param datasets
     */
    createLineChart: function (element, labels, datasets) {
        var _this = this;
        var options = _this.getChartOptions(element);
        var steppedValue = _this.getKeyOfObject(options, 'line.stepped', false);
        if (steppedValue !== 'after') {
            steppedValue = steppedValue === '1';
        }

        var tensionValue = 0.4;
        if (_this.getKeyOfObject(options, 'line.interpolation', 0) !== '1') {
            tensionValue = 0;
        }

        return new Chart(element.getContext('2d'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: _this.createDatasets(datasets, function (set) {
                    return {
                        label: set['label'],
                        data: set['data'],
                        backgroundColor: typeof set['border'] === 'object' ? set['border'][0] : '',
                        borderColor: typeof set['background'] === 'object' ? set['background'][0] : '',
                        fill: _this.getKeyOfObject(options, 'line.fill', 0) === '1',
                    }
                })
            },
            options: {
                legend: {
                    display: _this.getKeyOfObject(options, 'legend.active', 0) === '1',
                    position: _this.getKeyOfObject(options, 'legend.position', 'top')
                },
                scales: {
                    xAxes: [{
                        ticks: _this.getTicksConfig(options, 'x'),
                        scaleLabel: _this.getAxisLabelConfig(options, 'x'),
                    }],
                    yAxes: [{
                        stacked: _this.getKeyOfObject(options, 'line.stacked', 0) === '1',
                        ticks: _this.getTicksConfig(options, 'y'),
                        scaleLabel: _this.getAxisLabelConfig(options, 'y'),
                    }]
                },
                elements: {
                    line: {
                        stepped: steppedValue,
                        tension: tensionValue
                    }
                }
            }
        });
    },

    /**
     * build pie chart on element with labels and datasets
     *
     * @param element
     * @param labels
     * @param datasets
     */
    createPieChart: function (element, labels, datasets) {
        var _this = this;
        var options = _this.getChartOptions(element);
        return new Chart(element.getContext('2d'), {
            type: 'pie',
            data: {
                labels: labels,
                datasets: _this.createDatasets(datasets, function (set) {
                    return {
                        label: set['label'],
                        data: set['data'],
                        backgroundColor: typeof set['background'] === 'object' ? set['background'] : [],
                        borderWidth: 1
                    }
                })
            },
            options: {
                legend: {
                    display: _this.getKeyOfObject(options, 'legend.active', 0) === '1',
                    position: _this.getKeyOfObject(options, 'legend.position', 'top')
                },
            }
        });
    },

    /**
     * build doughnut chart on element with labels and datasets
     *
     * @param element
     * @param labels
     * @param datasets
     */
    createDoughnutChart: function (element, labels, datasets) {
        var _this = this;
        var options = _this.getChartOptions(element);
        var cutoutValue = parseInt(_this.getKeyOfObject(options, 'doughnut.cutoutPercentage', 75), 10);
        cutoutValue = cutoutValue <= 0 ? 75 : cutoutValue;

        return new Chart(element.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: _this.createDatasets(datasets, function (set) {
                    return {
                        label: set['label'],
                        data: set['data'],
                        backgroundColor: typeof set['background'] === 'object' ? set['background'] : [],
                        borderWidth: 1
                    }
                })
            },
            options: {
                legend: {
                    display: _this.getKeyOfObject(options, 'legend.active', 0) === '1',
                    position: _this.getKeyOfObject(options, 'legend.position', 'top')
                },
                cutoutPercentage: cutoutValue
            }
        });
    },

    /**
     * create datasets by given data and an additional mapping callback
     *
     * @param datasets
     * @param mapping
     * @returns array
     */
    createDatasets: function (datasets, mapping) {
        var keyValueMapping = mapping || null,
            processedDatasets = [];

        if (typeof keyValueMapping !== 'function') {
            return processedDatasets;
        }

        if (typeof datasets === 'object' && datasets.length > 0) {
            for (var i = 0; i < datasets.length; ++i) {
                var set = keyValueMapping(datasets[i]);
                processedDatasets.push(set);
            }
        }
        return processedDatasets;
    },

    getKeyOfObject: function (object, key, defaultValue) {
        var returnVal = key.split(".").reduce(function (o, x) {
            return (typeof o === 'undefined' || o === null) ? o : o[x];
        }, object);

        return typeof returnVal !== 'undefined' ? returnVal : defaultValue;
    },

    getAxisLabelConfig: function (config, axis) {
        var axisLabel = this.getKeyOfObject(config, 'axis.' + axis + '.label', '');
        if (axisLabel.length > 0) {
            return {
                display: true,
                labelString: axisLabel,
            };
        }
        return {};
    },

    getTicksConfig: function (config, axis) {
        var calculateAutomatic = this.getKeyOfObject(config, 'axis.' + axis + '.auto', 0) === '1';
        if (calculateAutomatic === true) {
            return {};
        }

        var ticksConf = {};
        var min = this.getKeyOfObject(config, 'axis.' + axis + '.min', null);
        if (min !== null) {
            ticksConf.min = parseInt(min, 10);
        }
        var max = this.getKeyOfObject(config, 'axis.' + axis + '.max', null);
        if (max !== null && max > min) {
            ticksConf.max = parseInt(max, 10);
        }
        var step = this.getKeyOfObject(config, 'axis.' + axis + '.step', null);
        if (step !== null && step > 0) {
            ticksConf.stepSize = parseInt(step, 10);
        }
        return ticksConf;
    },

    getChartOptions: function (element) {
        var chartConfig = {};
        try {
            chartConfig = JSON.parse(element.getAttribute('data-chart-config')) || {};
        } catch (e) {
        }

        if (Object.keys(chartConfig).length <= 0) {
            return {};
        }
        return chartConfig;
    }
};

document.addEventListener("DOMContentLoaded", function () {
    Hoogi91.Charts.init();
});
