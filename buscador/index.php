<?php
 $bid="20603319274";
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head id="j_idt2">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
    
    <link href="assets/img/favicon.ico" rel="icon" type="image/x-icon">
    <link href="assets/img/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <title>Consultar Comprobante Electrónico</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #CA3438 0%, #8B1E23 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #CA3438, #8B1E23);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .title {
            color: #CA3438;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .subtitle {
            color: #666;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #CA3438;
            background: white;
            box-shadow: 0 0 0 3px rgba(202, 52, 56, 0.1);
        }
        
        .form-input:read-only {
            background: #e9ecef;
            color: #6c757d;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-col {
            flex: 1;
        }
        
        .form-col-4 {
            flex: 0 0 35%;
        }
        
        .form-col-8 {
            flex: 0 0 65%;
        }
        
        .form-select {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .form-select:focus {
            outline: none;
            border-color: #CA3438;
            background: white;
            box-shadow: 0 0 0 3px rgba(202, 52, 56, 0.1);
        }
        
        .btn-primary {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #CA3438, #8B1E23);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(202, 52, 56, 0.3);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .footer-text {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        
        .footer-logo {
            height: 40px;
            opacity: 0.6;
            filter: grayscale(100%);
            transition: all 0.3s ease;
        }
        
        .footer-logo:hover {
            opacity: 1;
            filter: grayscale(0%);
        }
        
        .loading {
            display: none;
        }
        
        .loading.active {
            display: inline-block;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .alert {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .form-col-4,
            .form-col-8 {
                flex: 1;
            }
            
            .title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">
                <i class="fas fa-file-invoice"></i>
                Consulta Comprobante Electrónico
            </h1>
            <p class="subtitle">Ingrese los datos del comprobante a consultar</p>
        </div>
        
        <div id="alert" class="alert"></div>
        
        <form id="formLogin" name="formLogin">
            <div class="form-group">
                <label class="form-label" for="rucemisor">
                    <i class="fas fa-building"></i> RUC Emisor
                </label>
                <input id="rucemisor" name="rucemisor" value="<?=$bid;?>" type="text" 
                       maxlength="11" placeholder="RUC del emisor" tabindex="1" 
                       class="form-input" readonly>
            </div>
            
            <div class="form-row">
                <div class="form-col form-col-4">
                    <label class="form-label" for="serie">
                        <i class="fas fa-hashtag"></i> Serie
                    </label>
                    <input id="serie" name="serie" type="text" maxlength="4" 
                           placeholder="F001" tabindex="2" class="form-input" 
                           onkeyup="this.value = this.value.toUpperCase();" required>
                </div>
                
                <div class="form-col form-col-8">
                    <label class="form-label" for="correlativo">
                        <i class="fas fa-sort-numeric-up"></i> Correlativo
                    </label>
                    <input id="correlativo" name="correlativo" type="text" maxlength="8" 
                           placeholder="00002370" tabindex="3" class="form-input" required>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" for="consoleInner">
                    <i class="fas fa-file-alt"></i> Tipo de Documento
                </label>
                <select id="consoleInner" name="consoleInner" class="form-select" tabindex="4" required>
                    <option value="">Seleccione el tipo de documento</option>
                    <option value="2">FACTURA</option>
                    <option value="1">BOLETA DE VENTA</option>
                    <option value="3">NOTA DE CRÉDITO</option>
                    <option value="4">NOTA DE DÉBITO</option>
                    <option value="11">GUÍA DE REMISIÓN REMITENTE</option>
                </select>
            </div>
            
            <button id="loginButton" name="loginButton" class="btn-primary" type="submit" tabindex="5">
                <i class="fas fa-search"></i>
                <span class="btn-text">Consultar Comprobante</span>
                <i class="fas fa-spinner loading" id="loading"></i>
            </button>
        </form>
        
        <div class="footer">
            <p class="footer-text">Desarrollado por:</p>
            <img class="footer-logo" src="assets/img/magus.png" alt="Magus Technologies">
        </div>
    </div>

    <script type="text/javascript" src="assets/js/jquery.js"></script>
    <script type="text/javascript" src="assets/js/core.js"></script>
    <script type="text/javascript" src="assets/js/components.js"></script>
    
    <script>
    $(document).ready(function() {
        $("#formLogin").trigger("reset");
        
        function showAlert(message, type = 'error') {
            const alert = $('#alert');
            alert.removeClass('alert-error alert-success');
            alert.addClass('alert-' + type);
            alert.text(message);
            alert.show();
            
            setTimeout(() => {
                alert.hide();
            }, 5000);
        }
        
        $('#formLogin').submit(function(e){
            e.preventDefault();
            
            const serie = $.trim($('#serie').val());
            const correlativo = $.trim($('#correlativo').val());
            const rucemisor = $.trim($('#rucemisor').val());
            const consoleInner = $.trim($('#consoleInner').val());
            
            if (!serie || !correlativo || !consoleInner) {
                showAlert('Por favor, complete todos los campos requeridos.');
                return false;
            }
            
            // Mostrar loading
            $('#loading').addClass('active');
            $('.btn-text').text('Buscando...');
            $('#loginButton').prop('disabled', true);
            
            $.ajax({    
                url: "assets/functions/Buscar.php",
                type: "POST",
                datatype: "json",
                data: {
                    opcion: 1,
                    serie: serie,
                    correlativo: correlativo,
                    consoleInner: consoleInner,
                    rucemisor: rucemisor
                },
                success: function(data) {
                    try {
                        const ObjetoJS = JSON.parse(data);
                        
                        for (let item of ObjetoJS) {
                            const vruta = item.ruta;
                            const vfiltro = item.buscar;
                            const error = item.error || '';
                            
                            if (vfiltro > 0) {
                                showAlert('Comprobante encontrado. Abriendo PDF...', 'success');
                                setTimeout(() => {
                                    window.open(vruta, '_blank');
                                }, 1000);
                            } else {
                                if (error) {
                                    showAlert('Error: ' + error);
                                } else {
                                    showAlert('No se encontraron datos para los criterios especificados.');
                                }
                            }
                        }
                    } catch (e) {
                        showAlert('Error al procesar la respuesta del servidor.');
                    }
                },
                error: function() {
                    showAlert('Error de conexión. Por favor, intente nuevamente.');
                },
                complete: function() {
                    // Ocultar loading
                    $('#loading').removeClass('active');
                    $('.btn-text').text('Consultar Comprobante');
                    $('#loginButton').prop('disabled', false);
                }
            });
        });  
    });
    </script>
</body>
</html>
