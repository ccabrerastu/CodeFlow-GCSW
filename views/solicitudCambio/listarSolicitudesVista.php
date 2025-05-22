<?php include __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Gestión de Solicitudes de Cambio</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Solicitudes de Cambio</h1>
            <a href="index.php?c=SolicitudCambio&a=mostrarFormularioCrear"
               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Nueva Solicitud
            </a>
        </div>

        <?php if (!empty($status)): ?>
            <div class="mb-4 p-3 rounded <?= $status['type']==='success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= htmlspecialchars($status['text']) ?>
            </div>
        <?php endif; ?>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">ID</th>
                    <th class="p-2">Título</th>
                    <th class="p-2">Proyecto</th>
                    <th class="p-2">Solicitante</th>
                    <th class="p-2">Fecha</th>
                    <th class="p-2">Estado</th>
                    <th class="p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($solicitudes)): ?>
                    <?php foreach ($solicitudes as $sol): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="p-2"><?= htmlspecialchars($sol['id_solicitud']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($sol['titulo']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($sol['nombre_proyecto']) ?></td>
                            <td class="p-2"><?= htmlspecialchars($sol['nombre_completo']) ?></td>
                            <td class="p-2"><?= date('d/m/Y', strtotime($sol['fecha_creacion'])) ?></td>
                            <td class="p-2"><?= htmlspecialchars($sol['estado']) ?></td>
                            <td class="p-2 space-x-2">
                                <a href="index.php?c=SolicitudCambio&a=detalle&id_solicitud=<?= $sol['id_solicitud'] ?>"
                                   class="text-blue-600 hover:underline text-sm">Ver</a>
                                <a href="index.php?c=SolicitudCambio&a=mostrarFormularioEditar&id_solicitud=<?= $sol['id_solicitud'] ?>"
                                   class="text-orange-600 hover:underline text-sm">Editar</a>
                                <a href="index.php?c=SolicitudCambio&a=eliminar&id_solicitud=<?= $sol['id_solicitud'] ?>"
                                   onclick="return confirm('¿Eliminar esta solicitud?');"
                                   class="text-red-600 hover:underline text-sm">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-500">
                            No hay solicitudes registradas.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
