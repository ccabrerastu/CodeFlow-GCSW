<div class="bg-white p-6 rounded-2xl shadow-lg ring-1 ring-gray-200 text-sm">
    <h2 class="text-xl font-semibold text-blue-700 mb-6 flex items-center">
        <i class="fas fa-folder-open mr-2 text-blue-500 text-base"></i> Información General del Proyecto
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-800">
        <!-- Columna Izquierda -->
        <div class="space-y-3">
            <div class="p-3 bg-blue-50 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-blue-800">ID Proyecto</p>
                <p class="text-sm"><?= htmlspecialchars($proyecto['id_proyecto']) ?></p>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-blue-800">Nombre</p>
                <p class="text-sm"><?= htmlspecialchars($proyecto['nombre_proyecto']) ?></p>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-blue-800">Descripción</p>
                <p class="text-sm whitespace-pre-line"><?= nl2br(htmlspecialchars($proyecto['descripcion'] ?? 'N/A')) ?></p>
            </div>
        </div>

        <!-- Columna Derecha -->
        <div class="space-y-3">
            <div class="p-3 bg-blue-50 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-blue-800">Metodología</p>
                <p class="text-sm"><?= htmlspecialchars($proyecto['nombre_metodologia'] ?? 'No asignada') ?></p>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-blue-800">Product Owner</p>
                <p class="text-sm"><?= htmlspecialchars($proyecto['nombre_product_owner'] ?? 'No asignado') ?></p>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-blue-800">Fecha Inicio Planificada</p>
                <p class="text-sm"><?= htmlspecialchars($proyecto['fecha_inicio_planificada'] ? date('d/m/Y', strtotime($proyecto['fecha_inicio_planificada'])) : 'N/A') ?></p>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-blue-800">Fecha Fin Planificada</p>
                <p class="text-sm"><?= htmlspecialchars($proyecto['fecha_fin_planificada'] ? date('d/m/Y', strtotime($proyecto['fecha_fin_planificada'])) : 'N/A') ?></p>
            </div>
            <div class="p-3 bg-blue-50 rounded-lg shadow-sm">
                <p class="text-xs font-semibold text-blue-800">Estado</p>
                <p class="text-sm"><?= htmlspecialchars($proyecto['estado_proyecto']) ?></p>
            </div>
        </div>
    </div>

    <div class="mt-6 text-right">
        <a href="index.php?c=Proyecto&a=mostrarFormularioProyecto&id_proyecto=<?= $proyecto['id_proyecto'] ?>" 
           class="inline-flex items-center px-4 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-all">
            <i class="fas fa-edit mr-2 text-xs"></i> Editar Datos Generales
        </a>
    </div>
</div>
