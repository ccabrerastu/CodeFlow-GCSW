<?php
// Aquí iría tu lógica PHP de carga de $metodologias, etc.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Gestión de Metodologías</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js + Luxon -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.3.0/build/global/luxon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.3.1/dist/chartjs-adapter-luxon.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body class="bg-gradient-to-tr from-gray-100 to-white text-gray-800 min-h-screen">

    <?php include __DIR__ . '/partials/header.php'; ?>

    <div class="max-w-7xl mx-auto p-6">
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h1 class="text-4xl font-bold text-gray-800">📊 Gestión de Metodologías</h1>
        </div>

        <?php if (isset($statusMessage) && $statusMessage): ?>
            <div class="mb-4 px-4 py-3 rounded border 
                <?= $statusMessage['type'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700'; ?>">
                <?= htmlspecialchars($statusMessage['text']) ?>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto rounded-lg shadow mb-8 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-indigo-600 text-white text-left">
                    <tr>
                        <th class="px-6 py-3 font-medium">ID</th>
                        <th class="px-6 py-3 font-medium">Nombre</th>
                        <th class="px-6 py-3 font-medium">Descripción</th>
                        <th class="px-6 py-3 font-medium text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($metodologias)): ?>
                        <?php foreach ($metodologias as $index => $metodologia): ?>
                            <tr class="<?= $index % 2 === 0 ? 'bg-gray-50' : 'bg-white'; ?> hover:bg-indigo-50">
                                <td class="px-6 py-4"><?= htmlspecialchars($metodologia['id_metodologia']) ?></td>
                                <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($metodologia['nombre_metodologia']) ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($metodologia['descripcion'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4 text-center">
                                    <a href="index.php?c=FasesMetodologia&a=listarPorMetodologia&id_metodologia=<?= $metodologia['id_metodologia'] ?>" 
                                       class="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 text-sm">
                                       <i class="fas fa-tasks"></i> Fases
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-6">No hay metodologías registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

    <?php include __DIR__ . '/partials/footer.php'; ?>

</body>
</html>
