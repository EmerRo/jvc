<!-- resources\views\fragment-views\cliente\documentos\componentes\tabs-navegacion.php -->
<ul class="nav nav-tabs mb-4" id="archivosTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="fichas-tab" data-bs-toggle="tab" data-bs-target="#fichas" type="button" role="tab" aria-controls="fichas" aria-selected="true" onclick="cargarArchivos('ficha_tecnica')">
            Fichas Técnicas
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="informes-tab" data-bs-toggle="tab" data-bs-target="#informes" type="button" role="tab" aria-controls="informes" aria-selected="false" onclick="cargarArchivos('informe')">
            Informes
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="cartas-tab" data-bs-toggle="tab" data-bs-target="#cartas" type="button" role="tab" aria-controls="cartas" aria-selected="false" onclick="cargarArchivos('carta')">
            Cartas
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="constancias-tab" data-bs-toggle="tab" data-bs-target="#constancias" type="button" role="tab" aria-controls="constancias" aria-selected="false" onclick="cargarArchivos('constancia')">
            Constancias
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="internos-tab" data-bs-toggle="tab" data-bs-target="#internos" type="button" role="tab" aria-controls="internos" aria-selected="false" onclick="cargarArchivos('interno')">
            Archivos Internos
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="otros-tab" data-bs-toggle="tab" data-bs-target="#otros" type="button" role="tab" aria-controls="otros" aria-selected="false" onclick="cargarArchivos('otro')">
            Otros
        </button>
    </li>
</ul>