<?php
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Gestionar Fases de: <?= htmlspecialchars($metodologia['nombre_metodologia'] ?? 'Desconocida') ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gradient-to-tr from-blue-50 via-white to-blue-100 min-h-screen font-sans">
    <?php include __DIR__ . '/partials/header.php'; ?>

    <main class="max-w-5xl mx-auto mt-10 p-6 bg-white shadow-2xl rounded-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-3xl font-bold text-gray-800">
                Fases de Metodologia <span class="text-blue-600"><?= htmlspecialchars($metodologia['nombre_metodologia'] ?? 'Metodología Desconocida') ?></span>
            </h1>
            <a href="index.php?c=FasesMetodologia&a=mostrarFormularioCrear&id_metodologia=<?= htmlspecialchars($metodologia['id_metodologia'] ?? '') ?>"
               class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg shadow transition-all">
                <i class="fas fa-plus mr-2"></i> Nueva Fase
            </a>
        </div>

       <div class="mb-6">
    <a href="index.php?c=Metodologia&a=index"
       class="inline-flex items-center px-4 py-2 bg-white border border-blue-300 text-blue-600 rounded-lg shadow-sm hover:bg-blue-50 hover:text-blue-700 transition duration-150 text-sm font-medium">
        <i class="fas fa-arrow-left mr-2"></i> Volver a Metodologías
    </a>
</div>

        <?php if (isset($statusMessage) && $statusMessage): ?>
            <div class="p-3 rounded-lg mb-4 text-sm font-medium <?= $statusMessage['type'] === 'success' ? 'bg-green-100 text-green-700 border border-green-300' : 'bg-red-100 text-red-700 border border-red-300' ?>">
                <?= htmlspecialchars($statusMessage['text']) ?>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto rounded-lg shadow mt-6">
            <table class="w-full bg-white border border-gray-200 text-sm">
                <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-6 py-3 text-left">Orden</th>
                        <th class="px-6 py-3 text-left">Nombre</th>
                        <th class="px-6 py-3 text-left">Descripción</th>
                        <th class="px-6 py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    <?php if (!empty($fasesecsB)): ?>
                        <?php foreach ($fasesecsB as $index => $fase): ?>
                            <tr class="<?= $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' ?> border-b hover:bg-blue-50 transition">
                                <td class="px-6 py-4 font-bold text-blue-600 text-lg text-center"><?= htmlspecialchars($fase['orden']); ?></td>
                                <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($fase['nombre_fase']); ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($fase['descripcion'] ?? 'Sin descripción'); ?></td>
                                <td class="px-6 py-4 text-center space-x-1">
                                    <button onclick="toggleEcs(this, 'ecs-row-<?= $fase['id_fase_metodologia'] ?>')" 
                                            class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition text-xs">
                                        <i class="fas fa-cubes mr-1"></i> Ver ECS (<?= count($fase['elementos'] ?? []) ?>)
                                        <i class="fas fa-chevron-down ml-2 arrow-icon transition-transform"></i>
                                    </button>
                                    <a href="index.php?c=FasesMetodologia&a=mostrarFormularioEditar&id_fase=<?= $fase['id_fase_metodologia'] ?>"
                                       class="inline-flex items-center px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200 transition text-xs">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </a>
                                </td>
                            </tr>

                            <!-- Fila desplegable de ECS -->
                            <tr class="ecs-details-row hidden" id="ecs-row-<?= $fase['id_fase_metodologia'] ?>">
                                <td colspan="4" class="bg-gray-50 px-6 py-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <h2 class="text-gray-800 font-medium">ECS de esta fase</h2>
                                        <a href="#" class="text-blue-600 hover:underline text-sm">
                                            <i class="fas fa-plus-circle mr-1"></i> Asociar nuevo ECS
                                        </a>
                                    </div>

                                    <?php if (!empty($fase['elementos'])): ?>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                                            <?php foreach ($fase['elementos'] as $ecs): ?>
                                                <div class="p-4 border border-gray-200 rounded-lg bg-white shadow-sm flex justify-between items-center">
                                                    <div>
                                                        <p class="font-semibold text-gray-700"><?= htmlspecialchars($ecs['nombre_ecs']) ?></p>
                                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($ecs['tipo_ecs'] ?? 'Genérico') ?></p>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-medium">v<?= htmlspecialchars($ecs['version_actual']) ?></span>
                                                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-700 font-medium"><?= htmlspecialchars($ecs['estado_ecs']) ?></span>
                                                        <button title="Quitar asociación" class="text-red-500 hover:text-red-700">
                                                            <i class="fas fa-times-circle"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <p class="italic text-gray-500 text-center mt-4">No hay ECS registrados para esta fase.</p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-6 text-gray-500 italic">No se han registrado fases aún.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
        function toggleEcs(button, rowId) {
            const row = document.getElementById(rowId);
            const icon = button.querySelector('.arrow-icon');

            document.querySelectorAll('.ecs-details-row').forEach(r => r.classList.add('hidden'));
            document.querySelectorAll('.arrow-icon').forEach(i => i.classList.remove('rotate-180'));

            if (row.classList.contains('hidden')) {
                row.classList.remove('hidden');
                icon.classList.add('rotate-180');
            }
        }
    </script>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
