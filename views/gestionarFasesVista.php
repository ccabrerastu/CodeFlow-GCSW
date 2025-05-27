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

        <div class="space-y-6">
    <?php if (!empty($fasesecs)): ?>
        <?php foreach ($fasesecs as $fase): ?>
            <section class="bg-indigo-50 p-4 rounded-md border border-indigo-200">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="text-lg font-semibold text-indigo-700 flex items-center">
                        <i class="fas fa-layer-group mr-2"></i>
                        Fase <?= htmlspecialchars($fase['orden']) ?>: <?= htmlspecialchars($fase['nombre_fase']) ?>
                    </h4>
                    <a href="index.php?c=FasesMetodologia&a=mostrarFormularioEditar&id_fase=<?= $fase['id_fase_metodologia'] ?>" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        <i class="fas fa-edit mr-1"></i> Editar
                    </a>
                </div>
                <p class="text-gray-700 mb-3"><?= htmlspecialchars($fase['descripcion'] ?? 'Sin descripción') ?></p>

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

    <?php include __DIR__ . '/partials/footer.php';?>
</body>
</html>
