<?php
// Este archivo es para depurar problemas con Highcharts
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depuración de Highcharts</title>
    <!-- Cargar solo lo esencial para la prueba -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
    // Verificar si Highcharts ya está cargado
    if (typeof Highcharts === 'undefined') {
        // Crear elemento script
        var script = document.createElement('script');
        script.src = 'https://code.highcharts.com/highcharts.js';
        script.type = 'text/javascript';
        script.onload = function() {
            console.log('Highcharts cargado dinámicamente');
            // Si tienes una función de inicialización, llámala aquí
            if (typeof inicializarGraficos === 'function') {
                inicializarGraficos();
            }
        };
        // Agregar script al head
        document.head.appendChild(script);
    }
</script>
</head>
<body>
    <div id="container" style="width:100%; height:400px;"></div>
    
    <script>
        // Verificar si Highcharts se cargó correctamente
        console.log('¿Highcharts está definido?', typeof Highcharts !== 'undefined');
        
        // Intentar crear un gráfico simple
        document.addEventListener('DOMContentLoaded', function() {
            try {
                if (typeof Highcharts === 'undefined') {
                    document.getElementById('container').innerHTML = 
                        '<div style="color:red">Error: Highcharts no está disponible</div>';
                    return;
                }
                
                Highcharts.chart('container', {
                    chart: {
                        type: 'bar'
                    },
                    title: {
                        text: 'Prueba de Highcharts'
                    },
                    xAxis: {
                        categories: ['Manzanas', 'Naranjas', 'Peras', 'Plátanos', 'Uvas']
                    },
                    yAxis: {
                        title: {
                            text: 'Consumo de frutas'
                        }
                    },
                    series: [{
                        name: 'Jane',
                        data: [1, 0, 4, 2, 3]
                    }, {
                        name: 'John',
                        data: [5, 7, 3, 2, 1]
                    }]
                });
                
                console.log('Gráfico creado con éxito');
            } catch (error) {
                console.error('Error al crear el gráfico:', error);
                document.getElementById('container').innerHTML = 
                    '<div style="color:red">Error: ' + error.message + '</div>';
            }
        });
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, verificando Highcharts...');
    
    if (typeof Highcharts === 'undefined') {
        console.error('ERROR: Highcharts no está definido. Intentando cargar manualmente...');
        
        var script = document.createElement('script');
        script.src = 'https://code.highcharts.com/highcharts.js';
        script.onload = function() {
            console.log('Highcharts cargado manualmente con éxito.');
            if (typeof inicializarGraficos === 'function') {
                console.log('Llamando a inicializarGraficos()...');
                inicializarGraficos();
            } else {
                console.error('La función inicializarGraficos no está definida.');
            }
        };
        script.onerror = function() {
            console.error('Error al cargar Highcharts manualmente.');
            document.querySelectorAll('[id$="Chart"]').forEach(function(container) {
                container.innerHTML = '<div class="alert alert-danger">Error: No se pudo cargar la biblioteca de gráficos.</div>';
            });
        };
        document.head.appendChild(script);
    } else {
        console.log('Highcharts está disponible.');
        if (typeof inicializarGraficos === 'function') {
            console.log('Llamando a inicializarGraficos()...');
            inicializarGraficos();
        }
    }
});
</script>
</body>
</html>