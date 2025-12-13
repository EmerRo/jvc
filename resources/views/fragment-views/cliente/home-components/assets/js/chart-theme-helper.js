/**
 * Helper para tema oscuro en gráficas Highcharts
 * Funciones compartidas para todos los gráficos del dashboard
 */

// Detectar si está en modo oscuro
window.isDarkMode = function() {
    return document.body.classList.contains('dark-mode') ||
           document.body.getAttribute('data-layout-mode') === 'dark';
};

// Obtener colores según el modo
window.getChartColors = function() {
    const dark = window.isDarkMode();
    return {
        backgroundColor: dark ? '#2a3042' : '#ffffff',
        textColor: dark ? '#adb5bd' : '#6c757d',
        gridLineColor: dark ? '#3e4555' : '#e0e0e0',
        tooltipBg: dark ? '#3e4555' : '#ffffff',
        tooltipBorder: dark ? '#4b5563' : '#cccccc',
        tooltipTextColor: dark ? '#ced4da' : '#333333'
    };
};

// Aplicar opciones comunes de tema oscuro a un gráfico Highcharts
window.getChartOptions = function(baseOptions) {
    const chartColors = window.getChartColors();

    // Configuración base para modo oscuro
    const darkModeOptions = {
        chart: {
            backgroundColor: chartColors.backgroundColor,
            ...(baseOptions.chart || {})
        },
        xAxis: {
            ...(baseOptions.xAxis || {}),
            labels: {
                style: {
                    color: chartColors.textColor,
                    ...(baseOptions.xAxis?.labels?.style || {})
                },
                ...(baseOptions.xAxis?.labels || {})
            },
            lineColor: chartColors.gridLineColor,
            tickColor: chartColors.gridLineColor
        },
        yAxis: {
            ...(baseOptions.yAxis || {}),
            labels: {
                style: {
                    color: chartColors.textColor,
                    ...(baseOptions.yAxis?.labels?.style || {})
                },
                ...(baseOptions.yAxis?.labels || {})
            },
            title: {
                style: {
                    color: chartColors.textColor
                },
                ...(baseOptions.yAxis?.title || {})
            },
            gridLineColor: chartColors.gridLineColor,
            gridLineDashStyle: 'Dash'
        },
        tooltip: {
            backgroundColor: chartColors.tooltipBg,
            borderColor: chartColors.tooltipBorder,
            style: {
                color: chartColors.tooltipTextColor
            },
            ...(baseOptions.tooltip || {})
        },
        legend: {
            itemStyle: {
                color: chartColors.textColor
            },
            ...(baseOptions.legend || {})
        },
        plotOptions: {
            ...(baseOptions.plotOptions || {})
        }
    };

    // Merge profundo de las opciones
    return Object.assign({}, baseOptions, darkModeOptions);
};
