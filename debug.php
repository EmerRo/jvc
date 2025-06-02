<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - JVC</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Highcharts para gráficos -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos básicos para animaciones y colores personalizados */
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #f72585;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f94144;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .5; }
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .animate-fade-in { animation: fadeIn 0.5s ease-in-out; }
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
        .animate-slide-in-left { animation: slideInLeft 0.5s ease-out; }
        .animate-slide-in-right { animation: slideInRight 0.5s ease-out; }
        .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        .animate-spin { animation: spin 1s ease-in-out infinite; }
        
        .bg-primary { background-color: var(--primary-color); }
        .bg-secondary { background-color: var(--secondary-color); }
        .bg-success { background-color: var(--success-color); }
        .bg-warning { background-color: var(--warning-color); }
        .bg-danger { background-color: var(--danger-color); }
        
        .text-primary { color: var(--primary-color); }
        .text-secondary { color: var(--secondary-color); }
        .text-success { color: var(--success-color); }
        .text-warning { color: var(--warning-color); }
        .text-danger { color: var(--danger-color); }
        
        .border-primary { border-color: var(--primary-color); }
        
        .tab-active { color: var(--primary-color); border-bottom: 2px solid var(--primary-color); }
        
        .shadow-card {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="font-sans bg-gray-50">
    <div class="p-4 lg:p-6">
        <!-- start page title -->
        <div class="mb-6 animate-fade-in">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="md:w-2/3">
                    <h6 class="text-xl font-semibold">Dashboard</h6>
                    <ol class="mt-2 text-sm text-gray-600">
                        <li>Bienvenido <strong>JVC</strong> al Sistema de Facturación Electrónica <strong>HATUNA</strong></li>
                    </ol>
                </div>
                <div class="mt-4 md:mt-0 md:w-1/3 md:text-right">
                    <div class="inline-block relative">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md shadow-sm hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" id="dropdownButton">
                            <i class="fas fa-calendar-alt mr-2"></i> Período
                        </button>
                        <ul class="hidden absolute z-10 mt-1 w-48 bg-white rounded-md shadow-lg py-1 text-left right-0" id="periodDropdown">
                            <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="#">Hoy</a></li>
                            <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="#">Esta semana</a></li>
                            <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="#">Este mes</a></li>
                            <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="#">Este año</a></li>
                            <li><hr class="my-1 border-gray-200"></li>
                            <li><a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="#">Personalizado</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <!-- Tabs de navegación -->
        <ul class="flex flex-wrap border-b-2 border-gray-200 mb-6" id="dashboardTabs" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block py-3 px-4 text-gray-600 hover:text-primary font-medium relative tab-active" id="ventas-tab" data-tab="ventas" type="button" role="tab" aria-controls="ventas" aria-selected="true">
                    <i class="fas fa-chart-line mr-2"></i> Ventas
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block py-3 px-4 text-gray-600 hover:text-primary font-medium relative" id="productos-tab" data-tab="productos" type="button" role="tab" aria-controls="productos" aria-selected="false">
                    <i class="fas fa-box mr-2"></i> Productos
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block py-3 px-4 text-gray-600 hover:text-primary font-medium relative" id="stock-tab" data-tab="stock" type="button" role="tab" aria-controls="stock" aria-selected="false">
                    <i class="fas fa-warehouse mr-2"></i> Stock
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block py-3 px-4 text-gray-600 hover:text-primary font-medium relative" id="ingresos-egresos-tab" data-tab="ingresos-egresos" type="button" role="tab" aria-controls="ingresos-egresos" aria-selected="false">
                    <i class="fas fa-money-bill-wave mr-2"></i> Ingresos/Egresos
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block py-3 px-4 text-gray-600 hover:text-primary font-medium relative" id="clientes-tab" data-tab="clientes" type="button" role="tab" aria-controls="clientes" aria-selected="false">
                    <i class="fas fa-users mr-2"></i> Clientes
                </button>
            </li>
        </ul>

        <!-- Contenido de las pestañas -->
        <div class="tab-content" id="dashboardTabsContent">
            <!-- Tab de Ventas -->
            <div class="block tab-pane" id="ventas" role="tabpanel" aria-labelledby="ventas-tab">
                <!-- Tarjetas de resumen -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 animate-fade-in-up">
                    <div class="bg-white rounded-xl shadow-card overflow-hidden transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <div class="p-6">
                            <div class="mb-4">
                                <div class="absolute top-0 left-0 translate-x-4 translate-y-4 rounded-xl w-15 h-15">
                                    <img class="mt-3 mr-5" src="https://via.placeholder.com/50" alt="Icono">
                                </div>
                                <h5 class="text-right text-sm font-light uppercase">Monto Vendido</h5>
                                <h1 class="text-right text-3xl font-bold counter-value">S/ 45,250.00</h1>
                            </div>
                            <div class="pt-2">
                                <p class="mb-0 mt-1 text-right text-sm text-gray-600">Facturas y Boletas</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-card overflow-hidden transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <div class="p-6">
                            <div class="mb-4">
                                <div class="absolute top-0 left-0 translate-x-4 translate-y-4 rounded-xl w-15 h-15">
                                    <img class="mt-3 mr-5" src="https://via.placeholder.com/50" alt="Icono">
                                </div>
                                <h5 class="text-right text-sm font-light uppercase">Total en Facturas</h5>
                                <h1 class="text-right text-3xl font-bold counter-value">S/ 32,750.00</h1>
                            </div>
                            <div class="pt-2">
                                <p class="mb-0 mt-1 text-right text-sm text-gray-600">72.4% del total</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-card overflow-hidden transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <div class="p-6">
                            <div class="mb-4">
                                <div class="absolute top-0 left-0 translate-x-4 translate-y-4 rounded-xl w-15 h-15">
                                    <img class="mt-3 mr-5" src="https://via.placeholder.com/50" alt="Icono">
                                </div>
                                <h5 class="text-right text-sm font-light uppercase">Total en Boletas</h5>
                                <h1 class="text-right text-3xl font-bold counter-value">S/ 12,500.00</h1>
                            </div>
                            <div class="pt-2">
                                <p class="mb-0 mt-1 text-right text-sm text-gray-600">27.6% del total</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-card overflow-hidden transition-transform duration-300 hover:-translate-y-1 hover:shadow-lg">
                        <div class="p-6">
                            <div class="mb-4">
                                <div class="absolute top-0 left-0 translate-x-4 translate-y-4 rounded-xl w-15 h-15">
                                    <img class="mt-3 mr-5" src="https://via.placeholder.com/50" alt="Icono">
                                </div>
                                <h5 class="text-right text-sm font-light uppercase">Comparativa</h5>
                                <h1 class="text-right text-3xl font-bold counter-value">
                                    <i class="fas fa-arrow-up text-success"></i> 15.3%
                                </h1>
                            </div>
                            <div class="pt-2">
                                <p class="mb-0 mt-1 text-right text-sm text-gray-600">vs. Mes Anterior</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos de ventas -->
                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-6">
                    <div class="xl:col-span-8">
                        <div class="bg-white rounded-xl shadow-card overflow-hidden animate-slide-in-left">
                            <div class="p-6">
                                <h4 class="text-lg font-semibold mb-4">Ventas Anuales</h4>
                                <div class="relative h-[300px]">
                                    <div id="ventasAnualesChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                        <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                    </div>
                                    <div id="ventasAnualesChart" class="h-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="xl:col-span-4">
                        <div class="bg-white rounded-xl shadow-card overflow-hidden animate-slide-in-right">
                            <div class="p-6">
                                <h4 class="text-lg font-semibold mb-4">Comparativa Períodos</h4>
                                <div class="relative h-[300px]">
                                    <div id="comparativaChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                        <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                    </div>
                                    <div id="comparativaChart" class="h-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Comparativa con años anteriores -->
                <div class="mt-6">
                    <div class="bg-white rounded-xl shadow-card overflow-hidden animate-fade-in-up">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Comparativa con Años Anteriores</h4>
                            <div class="relative h-[300px]">
                                <div id="comparativaAnualChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                    <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                </div>
                                <div id="comparativaAnualChart" class="h-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab de Productos -->
            <div class="hidden tab-pane" id="productos" role="tabpanel" aria-labelledby="productos-tab">
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 animate-fade-in-up">
                    <div class="bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Productos Más Vendidos</h4>
                            <div class="relative h-[300px]">
                                <div id="productosTopChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                    <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                </div>
                                <div id="productosTopChart" class="h-full"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Distribución de Ventas por Producto</h4>
                            <div class="relative h-[300px]">
                                <div id="distribucionProductosChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                    <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                </div>
                                <div id="distribucionProductosChart" class="h-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="bg-white rounded-xl shadow-card overflow-hidden animate-slide-in-left">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Detalle de Productos Más Vendidos</h4>
                            <div class="overflow-x-auto rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Producto</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidades Vendidas</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ventas</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1001</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Lustradora Industrial</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Lustradora de alta potencia para uso industrial</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">350</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">S/ 87,500.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1002</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Aspiradora Industrial</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Aspiradora de alta potencia para uso industrial</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">290</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">S/ 72,500.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1003</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Limpiador Multiusos</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Limpiador concentrado para múltiples superficies</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">210</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">S/ 10,500.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1004</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Cepillo Industrial</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Cepillo resistente para limpieza industrial</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">180</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">S/ 9,000.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">1005</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Detergente Industrial</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Detergente concentrado para limpieza industrial</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">150</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">S/ 7,500.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab de Stock -->
            <div class="hidden tab-pane" id="stock" role="tabpanel" aria-labelledby="stock-tab">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 animate-fade-in-up">
                    <div class="bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Rotación de Inventario</h4>
                            <div class="relative h-[300px]">
                                <div id="rotacionInventarioChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                    <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                </div>
                                <div id="rotacionInventarioChart" class="h-full"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Estado del Stock</h4>
                            <div class="relative h-[300px]">
                                <div id="estadoStockChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                    <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                </div>
                                <div id="estadoStockChart" class="h-full"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Alertas de Stock</h4>
                            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-4 animate-pulse">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>¡Atención!</strong> Hay productos con stock crítico.
                            </div>
                            <ul class="divide-y divide-gray-200">
                                <li class="flex justify-between items-center py-3 px-2 bg-red-50">
                                    <div>
                                        <strong class="text-gray-900">Detergente Industrial</strong>
                                        <div class="text-xs text-gray-500">ID: 1005</div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        3 unidades
                                    </span>
                                </li>
                                <li class="flex justify-between items-center py-3 px-2 bg-red-50">
                                    <div>
                                        <strong class="text-gray-900">Cepillo Industrial</strong>
                                        <div class="text-xs text-gray-500">ID: 1004</div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        5 unidades
                                    </span>
                                </li>
                                <li class="flex justify-between items-center py-3 px-2">
                                    <div>
                                        <strong class="text-gray-900">Limpiador Multiusos</strong>
                                        <div class="text-xs text-gray-500">ID: 1003</div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        8 unidades
                                    </span>
                                </li>
                                <li class="flex justify-between items-center py-3 px-2">
                                    <div>
                                        <strong class="text-gray-900">Aspiradora Industrial</strong>
                                        <div class="text-xs text-gray-500">ID: 1002</div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        10 unidades
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="bg-white rounded-xl shadow-card overflow-hidden animate-slide-in-left">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Movimientos de Inventario</h4>
                            <div class="relative h-[300px]">
                                <div id="movimientosInventarioChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                    <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                </div>
                                <div id="movimientosInventarioChart" class="h-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab de Ingresos y Egresos -->
            <div class="hidden tab-pane" id="ingresos-egresos" role="tabpanel" aria-labelledby="ingresos-egresos-tab">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up">
                    <div class="bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Ingresos Mensuales</h4>
                            <h2 class="text-3xl font-bold text-green-500 counter-value">S/ 45,250.00</h2>
                            <div class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-green-500 h-full rounded-full" style="width: 100%"></div>
                            </div>
                            <p class="mt-3 text-sm text-gray-600">
                                <i class="fas fa-arrow-up text-green-500 mr-1"></i>
                                15.3% vs. mes anterior
                            </p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Egresos Mensuales</h4>
                            <h2 class="text-3xl font-bold text-red-500 counter-value">S/ 27,150.00</h2>
                            <div class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-red-500 h-full rounded-full" style="width: 60%"></div>
                            </div>
                            <p class="mt-3 text-sm text-gray-600">
                                <i class="fas fa-arrow-down text-red-500 mr-1"></i>
                                60.0% de los ingresos
                            </p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Ganancia Neta</h4>
                            <h2 class="text-3xl font-bold text-primary counter-value">S/ 18,100.00</h2>
                            <div class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-primary h-full rounded-full" style="width: 40%"></div>
                            </div>
                            <p class="mt-3 text-sm text-gray-600">
                                <i class="fas fa-check-circle text-primary mr-1"></i>
                                Margen de ganancia: 40.0%
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 mt-6">
                    <div class="xl:col-span-8">
                        <div class="bg-white rounded-xl shadow-card overflow-hidden animate-slide-in-left">
                            <div class="p-6">
                                <h4 class="text-lg font-semibold mb-4">Evolución de Ingresos y Egresos</h4>
                                <div class="relative h-[300px]">
                                    <div id="ingresosEgresosChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                        <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                    </div>
                                    <div id="ingresosEgresosChart" class="h-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="xl:col-span-4">
                        <div class="bg-white rounded-xl shadow-card overflow-hidden animate-slide-in-right">
                            <div class="p-6">
                                <h4 class="text-lg font-semibold mb-4">Distribución de Egresos</h4>
                                <div class="relative h-[300px]">
                                    <div id="distribucionEgresosChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                        <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                    </div>
                                    <div id="distribucionEgresosChart" class="h-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab de Clientes -->
            <div class="hidden tab-pane" id="clientes" role="tabpanel" aria-labelledby="clientes-tab">
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 animate-fade-in-up">
                    <div class="bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Clientes Top por Compras</h4>
                            <div class="relative h-[300px]">
                                <div id="clientesTopChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                    <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                </div>
                                <div id="clientesTopChart" class="h-full"></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-card overflow-hidden">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Frecuencia de Compra</h4>
                            <div class="relative h-[300px]">
                                <div id="frecuenciaCompraChartLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 rounded-lg z-10">
                                    <div class="w-10 h-10 border-4 border-gray-200 border-t-primary rounded-full animate-spin"></div>
                                </div>
                                <div id="frecuenciaCompraChart" class="h-full"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="bg-white rounded-xl shadow-card overflow-hidden animate-slide-in-left">
                        <div class="p-6">
                            <h4 class="text-lg font-semibold mb-4">Detalle de Clientes Top</h4>
                            <div class="overflow-x-auto rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frecuencia de Compra</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Compras</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Método de Pago</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2001</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Empresa A</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">12 compras</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">S/ 12,000.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Transferencia</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Al día</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2002</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Empresa B</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">8 compras</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">S/ 9,500.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Crédito</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pendiente</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2003</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Empresa C</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">6 compras</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">S/ 7,800.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Contado</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Al día</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2004</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Empresa D</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">5 compras</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">S/ 6,500.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Transferencia</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Atrasado</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2005</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Empresa E</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">4 compras</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">S/ 5,200.00</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Contado</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Al día</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-blue-500 hover:bg-opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Manejo de tabs personalizado
            const tabs = document.querySelectorAll('[data-tab]');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Desactivar todos los tabs
                    tabs.forEach(t => {
                        t.classList.remove('tab-active');
                        t.setAttribute('aria-selected', 'false');
                    });
                    
                    // Activar el tab actual
                    tab.classList.add('tab-active');
                    tab.setAttribute('aria-selected', 'true');
                    
                    // Ocultar todos los paneles
                    tabPanes.forEach(panel => {
                        panel.classList.add('hidden');
                    });
                    
                    // Mostrar el panel correspondiente
                    const panelId = tab.getAttribute('data-tab');
                    document.getElementById(panelId).classList.remove('hidden');
                });
            });

            // Manejo del dropdown de período
            const dropdownButton = document.getElementById('dropdownButton');
            const periodDropdown = document.getElementById('periodDropdown');
            
            if (dropdownButton && periodDropdown) {
                dropdownButton.addEventListener('click', () => {
                    periodDropdown.classList.toggle('hidden');
                });
                
                // Cerrar dropdown al hacer clic fuera
                document.addEventListener('click', (event) => {
                    if (!dropdownButton.contains(event.target) && !periodDropdown.contains(event.target)) {
                        periodDropdown.classList.add('hidden');
                    }
                });
            }

            // Datos simulados para los gráficos
            const dashboardData = {
                ventasAnuales: [25000, 28000, 32000, 30000, 35000, 42000, 45000, 48000, 42000, 38000, 35000, 45250],
                periodos: ['Diario', 'Semanal', 'Quincenal', 'Mensual', 'Bimestral', 'Trimestral', 'Semestral', 'Anual'],
                ventasPorPeriodo: [1508.33, 11312.50, 22625.00, 45250.00, 90500.00, 135750.00, 271500.00, 543000.00],
                productosNombres: ['Lustradora Industrial', 'Aspiradora Industrial', 'Limpiador Multiusos', 'Cepillo Industrial', 'Detergente Industrial'],
                productosCantidades: [350, 290, 210, 180, 150],
                clientesNombres: ['Empresa A', 'Empresa B', 'Empresa C', 'Empresa D', 'Empresa E'],
                clientesCompras: [12000, 9500, 7800, 6500, 5200]
            };

            // Verificar si Highcharts está disponible
            if (typeof Highcharts === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://code.highcharts.com/highcharts.js';
                script.onload = function () {
                    inicializarGraficos(dashboardData);
                };
                document.head.appendChild(script);
            } else {
                inicializarGraficos(dashboardData);
            }
            console.log('DOM cargado, inicializando gráficos con Highcharts...');
        });


function inicializarGraficos(dashboardData) {
        // Ocultar todos los spinners de carga
        document.querySelectorAll('[id$="ChartLoading"]').forEach(function (loader) {
            loader.style.display = 'none';
        });

        // Verificar si Highcharts está disponible
        if (typeof Highcharts === 'undefined') {
            console.error('Highcharts no está disponible. Comprueba la conexión a Internet o la inclusión del script.');
            document.querySelectorAll('[id$="Chart"]').forEach(function (container) {
                container.innerHTML = '<div class="bg-red-100 text-red-700 p-4 rounded-lg"><i class="fas fa-exclamation-triangle mr-2"></i>Error: Highcharts no está disponible.</div>';
            });
            return; // Detener ejecución
        }

        try {
            // Nombres de los meses
            const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            const mesesAbrev = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];

            // Colores para los gráficos
            const colors = {
                primary: '#4361ee',
                secondary: '#3f37c9',
                success: '#4cc9f0',
                danger: '#f94144',
                warning: '#f8961e',
                info: '#90e0ef',
                light: '#f8f9fa',
                dark: '#212529',
                purple: '#7209b7',
                pink: '#f72585',
                indigo: '#560bad',
                teal: '#2ec4b6',
                orange: '#ff9e00',
                yellow: '#ffbe0b'
            };

            // Configuración global de Highcharts
            Highcharts.setOptions({
                lang: {
                    thousandsSep: ',',
                    numericSymbols: ['k', 'M', 'G', 'T', 'P', 'E']
                },
                credits: {
                    enabled: false
                }
            });

            // Gráfico de Ventas Anuales
            Highcharts.chart('ventasAnualesChart', {
                chart: {
                    type: 'area',
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: meses,
                    labels: {
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    labels: {
                        formatter: function () {
                            return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                        },
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    },
                    gridLineDashStyle: 'Dash'
                },
                tooltip: {
                    formatter: function () {
                        return '<b>' + this.x + '</b><br>S/ ' + Highcharts.numberFormat(this.y, 2);
                    }
                },
                plotOptions: {
                    area: {
                        fillOpacity: 0.3,
                        marker: {
                            radius: 4,
                            lineWidth: 2,
                            lineColor: '#ffffff'
                        }
                    }
                },
                series: [{
                    name: 'Ventas',
                    color: colors.primary,
                    data: dashboardData.ventasAnuales
                }]
            });

            // Gráfico de Comparativa de Períodos
            Highcharts.chart('comparativaChart', {
                chart: {
                    type: 'column',
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: dashboardData.periodos,
                    labels: {
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    labels: {
                        formatter: function () {
                            return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                        },
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    },
                    gridLineDashStyle: 'Dash'
                },
                tooltip: {
                    formatter: function () {
                        return '<b>' + this.x + '</b><br>S/ ' + Highcharts.numberFormat(this.y, 2);
                    }
                },
                plotOptions: {
                    column: {
                        borderRadius: 5,
                        colorByPoint: true,
                        colors: [
                            colors.primary, colors.secondary, colors.success,
                            colors.warning, colors.danger, colors.info,
                            colors.purple, colors.pink
                        ]
                    }
                },
                series: [{
                    name: 'Ventas',
                    data: dashboardData.ventasPorPeriodo.map(parseFloat),
                    showInLegend: false
                }]
            });

            // Gráfico de Comparativa Anual (simulado)
            const añoActual = new Date().getFullYear();
            const años = [añoActual - 2, añoActual - 1, añoActual];

            // Generamos datos aleatorios para años anteriores basados en ventasAnuales
            const datosAñoAnterior = dashboardData.ventasAnuales.map(valor => valor * 0.8);
            const datosAñoAnteAnterior = dashboardData.ventasAnuales.map(valor => valor * 0.6);

            Highcharts.chart('comparativaAnualChart', {
                chart: {
                    type: 'line',
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: meses,
                    labels: {
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    labels: {
                        formatter: function () {
                            return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                        },
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    },
                    gridLineDashStyle: 'Dash'
                },
                tooltip: {
                    shared: true,
                    crosshairs: true,
                    formatter: function () {
                        let s = '<b>' + this.x + '</b>';

                        this.points.forEach(function (point) {
                            s += '<br/><span style="color:' + point.series.color + '">\u25CF</span> ' +
                                point.series.name + ': S/ ' + Highcharts.numberFormat(point.y, 2);
                        });

                        return s;
                    }
                },
                plotOptions: {
                    line: {
                        marker: {
                            radius: 4,
                            lineWidth: 2,
                            lineColor: '#ffffff'
                        }
                    }
                },
                series: [
                    {
                        name: años[0].toString(),
                        color: colors.secondary,
                        data: datosAñoAnteAnterior,
                        lineWidth: 2
                    },
                    {
                        name: años[1].toString(),
                        color: colors.warning,
                        data: datosAñoAnterior,
                        lineWidth: 2
                    },
                    {
                        name: años[2].toString(),
                        color: colors.primary,
                        data: dashboardData.ventasAnuales,
                        lineWidth: 3
                    }
                ]
            });

            // Gráfico de Productos Top
            Highcharts.chart('productosTopChart', {
                chart: {
                    type: 'bar',
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: dashboardData.productosNombres,
                    labels: {
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    labels: {
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    },
                    gridLineDashStyle: 'Dash'
                },
                tooltip: {
                    formatter: function () {
                        return '<b>' + this.x + '</b><br>' + this.y + ' unidades';
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 5,
                        colorByPoint: true,
                        colors: [
                            colors.primary, colors.secondary, colors.success,
                            colors.warning, colors.danger
                        ]
                    }
                },
                series: [{
                    name: 'Unidades Vendidas',
                    data: dashboardData.productosCantidades,
                    showInLegend: false
                }]
            });

            // Gráfico de Distribución de Productos (simulado)
            const categorias = ['Limpieza', 'Maquinaria', 'Accesorios', 'Químicos', 'Otros'];
            const porcentajes = [40, 25, 15, 12, 8];

            Highcharts.chart('distribucionProductosChart', {
                chart: {
                    type: 'pie',
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true,
                        innerSize: '60%'
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    itemStyle: {
                        color: '#6c757d',
                        fontWeight: 'normal',
                        fontSize: '12px'
                    }
                },
                series: [{
                    name: 'Porcentaje',
                    colorByPoint: true,
                    data: categorias.map((cat, index) => ({
                        name: cat,
                        y: porcentajes[index],
                        color: [colors.primary, colors.secondary, colors.info, colors.danger, colors.warning][index]
                    }))
                }]
            });

            // Gráfico de Rotación de Inventario (simulado)
            const productosRotacion = ['Lustradora Industrial', 'Aspiradora Industrial', 'Limpiador Multiusos', 'Cepillo Industrial', 'Detergente Industrial'];
            const rotacionDias = [15, 22, 8, 30, 5];

            Highcharts.chart('rotacionInventarioChart', {
                chart: {
                    type: 'column',
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: productosRotacion,
                    labels: {
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Días promedio de rotación',
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    },
                    labels: {
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    },
                    gridLineDashStyle: 'Dash'
                },
                tooltip: {
                    formatter: function () {
                        return '<b>' + this.x + '</b><br>' + this.y + ' días';
                    }
                },
                plotOptions: {
                    column: {
                        borderRadius: 5,
                        colorByPoint: true,
                        colors: [
                            colors.primary, colors.secondary, colors.success,
                            colors.warning, colors.danger
                        ]
                    }
                },
                series: [{
                    name: 'Días promedio',
                    data: rotacionDias,
                    showInLegend: false
                }]
            });

            // Gráfico de Estado del Stock (simulado)
            const estadosStock = ['Óptimo', 'Normal', 'Bajo', 'Crítico'];
            const cantidadesStock = [45, 30, 15, 10];

            Highcharts.chart('estadoStockChart', {
                chart: {
                    type: 'pie',
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    itemStyle: {
                        color: '#6c757d',
                        fontWeight: 'normal',
                        fontSize: '12px'
                    }
                },
                series: [{
                    name: 'Porcentaje',
                    colorByPoint: true,
                    data: [
                        { name: estadosStock[0], y: cantidadesStock[0], color: colors.success },
                        { name: estadosStock[1], y: cantidadesStock[1], color: colors.info },
                        { name: estadosStock[2], y: cantidadesStock[2], color: colors.warning },
                        { name: estadosStock[3], y: cantidadesStock[3], color: colors.danger }
                    ]
                }]
            });

            // Gráfico de Movimientos de Inventario (simulado)
            const mesesMovimientos = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
            const entradas = [120, 150, 180, 130, 160, 190];
            const salidas = [100, 130, 160, 120, 140, 170];

            Highcharts.chart('movimientosInventarioChart', {
                chart: {
                    type: 'areaspline',
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: mesesMovimientos,
                    labels: {
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: 'Unidades',
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    },
                    labels: {
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    },
                    gridLineDashStyle: 'Dash'
                },
                tooltip: {
                    shared: true,
                    crosshairs: true
                },
                plotOptions: {
                    areaspline: {
                        fillOpacity: 0.3,
                        marker: {
                            radius: 4,
                            lineWidth: 2,
                            lineColor: '#ffffff'
                        }
                    }
                },
                series: [
                    {
                        name: 'Entradas',
                        color: colors.success,
                        data: entradas
                    },
                    {
                        name: 'Salidas',
                        color: colors.danger,
                        data: salidas
                    }
                ]
            });

            // Gráfico de Ingresos y Egresos (simulado)
            const mesesFinanzas = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
            const ingresos = [5000, 5500, 6000, 5800, 6200, 6500];
            const egresos = [3000, 3300, 3600, 3500, 3700, 3900];
            const ganancias = ingresos.map((ingreso, index) => ingreso - egresos[index]);

            Highcharts.chart('ingresosEgresosChart', {
                chart: {
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: mesesFinanzas,
                    labels: {
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    labels: {
                        formatter: function () {
                            return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                        },
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    },
                    gridLineDashStyle: 'Dash'
                },
                tooltip: {
                    shared: true,
                    crosshairs: true,
                    formatter: function () {
                        let s = '<b>' + this.x + '</b>';

                        this.points.forEach(function (point) {
                            s += '<br/><span style="color:' + point.series.color + '">\u25CF</span> ' +
                                point.series.name + ': S/ ' + Highcharts.numberFormat(point.y, 2);
                        });

                        return s;
                    }
                },
                plotOptions: {
                    column: {
                        borderRadius: 5
                    }
                },
                series: [
                    {
                        name: 'Ingresos',
                        type: 'column',
                        color: colors.success,
                        data: ingresos
                    },
                    {
                        name: 'Egresos',
                        type: 'column',
                        color: colors.danger,
                        data: egresos
                    },
                    {
                        name: 'Ganancia',
                        type: 'spline',
                        color: colors.primary,
                        data: ganancias,
                        marker: {
                            lineWidth: 2,
                            lineColor: colors.primary,
                            fillColor: 'white'
                        }
                    }
                ]
            });

            // Gráfico de Distribución de Egresos (simulado)
            const categoriasEgresos = ['Compras', 'Salarios', 'Servicios', 'Impuestos', 'Otros'];
            const porcentajesEgresos = [45, 25, 15, 10, 5];

            Highcharts.chart('distribucionEgresosChart', {
                chart: {
                    type: 'pie',
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true,
                        innerSize: '60%'
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    itemStyle: {
                        color: '#6c757d',
                        fontWeight: 'normal',
                        fontSize: '12px'
                    }
                },
                series: [{
                    name: 'Porcentaje',
                    colorByPoint: true,
                    data: categoriasEgresos.map((cat, index) => ({
                        name: cat,
                        y: porcentajesEgresos[index],
                        color: [colors.primary, colors.secondary, colors.success, colors.warning, colors.danger][index]
                    }))
                }]
            });

            // Gráfico de Clientes Top
            Highcharts.chart('clientesTopChart', {
                chart: {
                    type: 'bar',
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                xAxis: {
                    categories: dashboardData.clientesNombres,
                    labels: {
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    labels: {
                        formatter: function () {
                            return 'S/ ' + Highcharts.numberFormat(this.value, 0);
                        },
                        style: {
                            color: '#6c757d',
                            fontSize: '12px'
                        }
                    },
                    gridLineDashStyle: 'Dash'
                },
                tooltip: {
                    formatter: function () {
                        return '<b>' + this.x + '</b><br>S/ ' + Highcharts.numberFormat(this.y, 2);
                    }
                },
                plotOptions: {
                    bar: {
                        borderRadius: 5,
                        colorByPoint: true,
                        colors: [
                            colors.primary, colors.secondary, colors.success,
                            colors.warning, colors.danger
                        ]
                    }
                },
                series: [{
                    name: 'Total Compras (S/)',
                    data: dashboardData.clientesCompras,
                    showInLegend: false
                }]
            });

            // Gráfico de Frecuencia de Compra (simulado)
            const frecuencias = ['Semanal', 'Quincenal', 'Mensual', 'Trimestral', 'Semestral', 'Anual'];
            const cantidadClientes = [5, 12, 25, 18, 10, 8];

            Highcharts.chart('frecuenciaCompraChart', {
                chart: {
                    type: 'pie',
                    style: {
                        fontFamily: 'Poppins, sans-serif'
                    },
                    animation: {
                        duration: 1000
                    }
                },
                title: {
                    text: null
                },
                tooltip: {
                    formatter: function () {
                        const total = cantidadClientes.reduce((a, b) => a + b, 0);
                        const percentage = (this.y / total * 100).toFixed(1);
                        return '<b>' + this.point.name + '</b><br>' +
                            this.y + ' clientes (' + percentage + '%)';
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: false
                        },
                        showInLegend: true
                    }
                },
                legend: {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    itemStyle: {
                        color: '#6c757d',
                        fontWeight: 'normal',
                        fontSize: '12px'
                    }
                },
                series: [{
                    name: 'Clientes',
                    colorByPoint: true,
                    data: frecuencias.map((freq, index) => ({
                        name: freq,
                        y: cantidadClientes[index],
                        color: [colors.primary, colors.secondary, colors.success,
                        colors.warning, colors.danger, colors.info][index]
                    }))
                }]
            });

            // Animación para los contadores
            try {
                const counterElements = document.querySelectorAll('.counter-value');
                counterElements.forEach(counter => {
                    const target = parseFloat(counter.textContent.replace(/[^\d.-]/g, ''));
                    const duration = 1500;
                    const step = target / (duration / 16);
                    let current = 0;

                    const updateCounter = () => {
                        if (current < target) {
                            current += step;
                            if (current > target) current = target;

                            // Formateamos el número según el formato original
                            const formatted = counter.textContent.replace(/[\d.,]+/, current.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                            counter.textContent = formatted;

                            requestAnimationFrame(updateCounter);
                        }
                    };

                    updateCounter();
                });
            } catch (e) {
                console.error('Error en la animación de contadores:', e);
            }

            console.log('Inicialización de gráficos completada con Highcharts');
        } catch (error) {
            console.error('Error al inicializar gráficos:', error);
            document.querySelectorAll('[id$="Chart"]').forEach(function (container) {
                container.innerHTML = '<div class="bg-red-100 text-red-700 p-4 rounded-lg"><i class="fas fa-exclamation-triangle mr-2"></i>Error al inicializar gráficos.</div>';
            });
        }
    }
    </script>
</body>

</html>