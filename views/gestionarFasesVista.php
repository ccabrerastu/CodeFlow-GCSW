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
                            <tr class="border-b hover:bg-gray-100 <?= ($index % 2 === 0) ? 'bg-white' : 'bg-gray-50'; ?>">
                                <td class="px-6 py-4"><?= htmlspecialchars($fase['orden']); ?></td>
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($fase['nombre_fase']); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($fase['descripcion'] ?? 'N/A'); ?></td>
                                <td class="px-6 py-4">
                                    <a href="index.php?c=FasesMetodologia&a=mostrarFormularioEditar&id_fase=<?= $fase['id_fase_metodologia'] ?>" class="btn btn-edit">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </a>
                                </td>
                            </tr>

                <?php if (!empty($fase['elementos'])): ?>
                    <div class="ml-3">
                        <p class="font-medium text-gray-800 mb-2">Actividades:</p>
                        <ul class="space-y-2 list-disc list-inside">
                            <?php foreach ($fase['elementos'] as $actividad): ?>
                                <li class="text-gray-700">
                                    <?= htmlspecialchars($actividad['nombre_ecs']) ?>
                                    <span class="text-sm text-gray-500">(<?= htmlspecialchars($actividad['estado_ecs']) ?>, v<?= htmlspecialchars($actividad['version_actual']) ?>)</span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <p class="italic text-gray-500 mt-1 text-sm ml-3">No hay actividades registradas para esta fase.</p>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center text-gray-500 italic">
            No hay fases registradas para esta metodología.
        </div>
    <?php endif; ?>
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
