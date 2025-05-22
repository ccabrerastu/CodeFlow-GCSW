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
            <a href="index.php?c=OrdenCambio&a=listar" class="btn btn-primary">
                <i class="fas fa-cogs mr-1"></i> Nueva Orden
            </a>
        </div>

        <?php if (isset($statusMessage)): ?>
            <div class="status-message <?= htmlspecialchars($statusMessage['type']) ?>">
                <?= htmlspecialchars($statusMessage['text']) ?>
            </div>
        <?php endif; ?>

        <div class="table-container overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Título</th>
                        <th class="px-6 py-3">Responsable</th>
                        <th class="px-6 py-3">Fecha</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ordenes)): foreach ($ordenes as $oc): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-6 py-4"><?= $oc['id_oc'] ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($oc['titulo_oc']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($oc['nombre_usuario']) ?></td>
                            <td class="px-6 py-4"><?= date('d/m/Y', strtotime($oc['fecha_creacion'])) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($oc['estado_oc']) ?></td>
                            <td class="px-6 py-4 space-x-2">
                                <a href="index.php?c=OrdenCambio&a=detalle&id_oc=<?= $oc['id_oc'] ?>" class="btn btn-secondary text-xs">
                                    <i class="fas fa-eye mr-1"></i> Ver
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; else: ?>
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
