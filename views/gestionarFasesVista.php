<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Gestionar Fases de Metodología: <?= htmlspecialchars($metodologia['nombre_metodologia'] ?? 'Desconocida') ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 900px; margin: 20px auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #e2e8f0; }
        tr:hover { background-color: #f1f5f9; }
        .btn { padding: 8px 12px; border-radius: 4px; text-decoration: none; display: inline-block; margin-right: 5px; font-size: 0.875rem; }
        .btn-primary { background-color: #4A90E2; color: white; }
        .btn-primary:hover { background-color: #357ABD; }
        .btn-edit { background-color: #F5A623; color: white; }
        .btn-edit:hover { background-color: #D9931F; }
        .btn-delete { background-color: #D0021B; color: white; }
        .btn-delete:hover { background-color: #B00216; }
        .status-message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .status-message.success { background-color: #e6fffa; border: 1px solid #38a169; color: #2f855a; }
        .status-message.error { background-color: #fed7d7; border: 1px solid #e53e3e; color: #c53030; }
        /* Estilos para la fila desplegable de ECS */
        .ecs-details-row { display: none; }
        .ecs-details-row.visible { display: table-row; }
        .ecs-card { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 0.75rem; display: flex; justify-content: space-between; align-items: center; }
        .arrow-icon { transition: transform 0.3s; }
        .arrow-icon.rotated { transform: rotate(180deg); }
    </style>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/partials/header.php'; ?>

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-700">
                Gestionar Fases de: <span class="text-blue-600"><?= htmlspecialchars($metodologia['nombre_metodologia'] ?? 'Metodología Desconocida') ?></span>
                
            </h1>
            <a href="index.php?c=FasesMetodologia&a=mostrarFormularioCrear&id_metodologia=<?= htmlspecialchars($metodologia['id_metodologia'] ?? '') ?>" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Nueva Fase
            </a>
        </div>
        <div class="mb-4">
            <a href="index.php?c=Metodologia&a=index" class="text-blue-600 hover:underline">&larr; Volver a Metodologías</a>
        </div>


        <?php if (isset($statusMessage) && $statusMessage): ?>
            <div class="status-message <?= htmlspecialchars($statusMessage['type']) ?>">
                <?= htmlspecialchars($statusMessage['text']) ?>
            </div>
        <?php endif; ?>

        <div class="table-container">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-3">Orden</th>
                        <th scope="col" class="px-6 py-3">Nombre Fase</th>
                        <th scope="col" class="px-6 py-3">Descripción</th>
                        <th scope="col" class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($fasesecsB)): ?>
                        <?php foreach ($fasesecsB as $index => $fase): ?>
                            <tr class="fase-row border-b hover:bg-gray-50">
                                <td class="px-6 py-4 text-center font-bold text-lg text-gray-500"><?= htmlspecialchars($fase['orden']); ?></td>
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($fase['nombre_fase']); ?></td>
                                <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($fase['descripcion'] ?? 'N/A'); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <button onclick="toggleEcs(this, 'ecs-row-<?= $fase['id_fase_metodologia'] ?>')" class="btn btn-secondary text-xs">
                                        <i class="fas fa-cubes mr-1"></i>
                                        Ver ECS (<?= count($fase['elementos'] ?? []) ?>)
                                        <i class="fas fa-chevron-down arrow-icon ml-2"></i>
                                    </button>
                                    <a href="index.php?c=FasesMetodologia&a=mostrarFormularioEditar&id_fase=<?= $fase['id_fase_metodologia'] ?>" class="btn btn-edit text-xs">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            
                            <!-- Fila oculta con los detalles de los ECS -->
                            <tr class="ecs-details-row" id="ecs-row-<?= $fase['id_fase_metodologia'] ?>">
                                <td colspan="4" class="p-0 bg-gray-50">
                                    <div class="p-4">
                                        <div class="flex justify-between items-center mb-3">
                                            <h5 class="font-semibold text-gray-700">ECS Predeterminados para esta Fase</h5>
                                            <a href="#" class="btn btn-primary btn-sm text-xs">
                                                <i class="fas fa-plus mr-1"></i> Asociar Nuevo ECS
                                            </a>
                                        </div>

                                        <?php if (!empty($fase['elementos'])): ?>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <?php foreach ($fase['elementos'] as $actividad): ?>
                                                    <div class="ecs-card">
                                                        <div>
                                                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($actividad['nombre_ecs']) ?></p>
                                                            <p class="text-xs text-gray-500"><?= htmlspecialchars($actividad['tipo_ecs'] ?? 'Genérico') ?></p>
                                                        </div>
                                                        <div class="flex items-center gap-2">
                                                            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs font-medium">
                                                                v<?= htmlspecialchars($actividad['version_actual']) ?>
                                                            </span>
                                                            <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs font-medium">
                                                                <?= htmlspecialchars($actividad['estado_ecs']) ?>
                                                            </span>
                                                            <button title="Quitar asociación" class="text-red-500 hover:text-red-700 ml-2">
                                                                <i class="fas fa-times-circle"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php else: ?>
                                            <p class="italic text-gray-500 text-center py-4">No hay ECS predefinidos para esta fase.</p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No hay fases registradas para esta metodología.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>

            </table>
        </div>
</div>

    </div>

       <script>
        function toggleEcs(button, rowId) {
            const ecsRow = document.getElementById(rowId);
            const arrowIcon = button.querySelector('.arrow-icon');
            if (ecsRow) {
                const isVisible = ecsRow.classList.contains('visible');
                // Cerrar todas las filas abiertas
                document.querySelectorAll('.ecs-details-row.visible').forEach(row => {
                    row.classList.remove('visible');
                });
                document.querySelectorAll('.arrow-icon.rotated').forEach(icon => {
                    icon.classList.remove('rotated');
                });
                
                // Abrir la fila actual si estaba cerrada
                if (!isVisible) {
                    ecsRow.classList.add('visible');
                    arrowIcon.classList.add('rotated');
                }
            }
        }
    </script>
    <?php include __DIR__ . '/partials/footer.php';?>
</body>
</html>
