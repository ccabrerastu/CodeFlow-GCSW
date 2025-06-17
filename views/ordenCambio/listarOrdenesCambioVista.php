<?php include __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Gestionar Órdenes de Cambio</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-700">Órdenes de Cambio</h1>
            
        </div>

        
        <?php if (!empty($status)): ?>
            <div class="status-message <?= htmlspecialchars($status['type']) ?>">
                <?= htmlspecialchars($status['text']) ?>
            </div>
        <?php endif; ?>

        <div class="table-container overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Título Solicitud</th>
                        <th class="px-6 py-3">Creada por</th>
                        <th class="px-6 py-3">Fecha</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ordenes)): ?>
                        <?php foreach ($ordenes as $oc): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4"><?= htmlspecialchars($oc['id_orden'] ?? '') ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($oc['titulo_solicitud'] ?? '') ?></td>
                                <td class="px-6 py-4"><?= htmlspecialchars($oc['nombre_creador'] ?? '') ?></td>
                                <td class="px-6 py-4">
                                    <?= !empty($oc['fecha_creacion'])
                                        ? date('d/m/Y', strtotime($oc['fecha_creacion']))
                                        : '' ?>
                                </td>
                                <td class="px-6 py-4"><?= htmlspecialchars($oc['estado'] ?? '') ?></td>
                                <td class="px-6 py-4 space-x-2">
                                    <a href="index.php?c=OrdenCambio&a=detalle&id=<?= urlencode($oc['id_orden'] ?? '') ?>"
                                       class="btn btn-secondary text-xs">
                                        <i class="fas fa-eye mr-1"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                No hay órdenes registradas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
