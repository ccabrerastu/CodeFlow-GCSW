<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Gestión de Proyectos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 1000px; margin: 20px auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #e2e8f0; }
        tr:hover { background-color: #f1f5f9; }
        .btn { padding: 8px 12px; border-radius: 4px; text-decoration: none; display: inline-block; margin-right: 5px; font-size: 0.875rem; }
        .btn-primary { background-color: #4A90E2; color: white; }
        .btn-primary:hover { background-color: #357ABD; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn-secondary:hover { background-color: #5a6268; }
        .status-message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .status-message.success { background-color: #e6fffa; border: 1px solid #38a169; color: #2f855a; }
        .status-message.error { background-color: #fed7d7; border: 1px solid #e53e3e; color: #c53030; }
    </style>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/partials/header.php'; ?>

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-700">Gestión de Proyectos</h1>
            <a href="index.php?c=Proyecto&a=mostrarFormularioProyecto" class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Nuevo Proyecto
            </a>
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
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Nombre del Proyecto</th>
                        <th scope="col" class="px-6 py-3">Descripción</th>
                        <th scope="col" class="px-6 py-3">Metodología</th>
                        <th scope="col" class="px-6 py-3">Product Owner</th>
                        <th scope="col" class="px-6 py-3">Estado</th>
                        <th scope="col" class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($proyectos)): ?>
                        <?php foreach ($proyectos as $index => $proyecto): ?>
                            <tr class="border-b hover:bg-gray-100 <?= ($index % 2 === 0) ? 'bg-white' : 'bg-gray-50'; ?>">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($proyecto['id_proyecto']); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($proyecto['nombre_proyecto']); ?></td>
                                <td class="px-6 py-4 truncate max-w-xs"><?= htmlspecialchars(substr($proyecto['descripcion'] ?? '', 0, 50)) . (strlen($proyecto['descripcion'] ?? '') > 50 ? '...' : ''); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($proyecto['nombre_metodologia'] ?? 'N/A'); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($proyecto['nombre_product_owner'] ?? 'N/A'); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($proyecto['estado_proyecto']); ?></td>
                                <td class="px-6 py-4">
                                    <a href="index.php?c=Proyecto&a=planificar&id_proyecto=<?= $proyecto['id_proyecto'] ?>" class="btn btn-secondary">
                                        <i class="fas fa-calendar-alt mr-1"></i> Planificar
                                    </a>
                                    <a href="index.php?c=Proyecto&a=mostrarFormularioProyecto&id_proyecto=<?= $proyecto['id_proyecto'] ?>" class="btn btn-edit">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </a>
                                    </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No hay proyectos registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include __DIR__ . '/partials/footer.php';  ?>
</body>
</html>
