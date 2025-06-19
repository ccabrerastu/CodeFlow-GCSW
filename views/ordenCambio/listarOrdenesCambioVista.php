<?php include __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Gestionar Órdenes de Cambio</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-2xl shadow-xl">
    <!-- Encabezado -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
            <i class="fas fa-clipboard-list text-blue-500"></i> Órdenes de Cambio
        </h1>
    </div>

    <!-- Mensaje de estado -->
    <?php if (!empty($status)): ?>
        <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium 
            <?= $status['type'] === 'success' 
                ? 'bg-green-100 text-green-800 border border-green-300' 
                : 'bg-red-100 text-red-800 border border-red-300' ?>">
            <i class="fas <?= $status['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
            <?= htmlspecialchars($status['text']) ?>
        </div>
    <?php endif; ?>

    <!-- Tabla -->
<div class="overflow-x-auto rounded-xl shadow-lg ring-1 ring-gray-200">
    <table class="w-full text-sm text-left text-gray-700 bg-white">
        <thead class="text-xs font-bold uppercase bg-gradient-to-r from-blue-100 via-blue-50 to-blue-100 text-blue-800 tracking-wider">
            <tr class="text-left">
                <th class="px-6 py-4"><i class="fas fa-file-alt mr-2 text-blue-500"></i> Título Solicitud</th>
                <th class="px-6 py-4"><i class="fas fa-user mr-2 text-blue-500"></i> Creada por</th>
                <th class="px-6 py-4"><i class="fas fa-calendar-alt mr-2 text-blue-500"></i> Fecha</th>
                <th class="px-6 py-4"><i class="fas fa-info-circle mr-2 text-blue-500"></i> Estado</th>
                <th class="px-6 py-4 text-center"><i class="fas fa-tools mr-2 text-blue-500"></i> Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if (!empty($ordenes)): ?>
                <?php foreach ($ordenes as $oc): ?>
                    <tr class="hover:bg-blue-50 transition-all duration-200 ease-in-out">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($oc['titulo_solicitud'] ?? '') ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($oc['nombre_creador'] ?? '') ?></td>
                        <td class="px-6 py-4">
                            <?= !empty($oc['fecha_creacion']) ? date('d/m/Y', strtotime($oc['fecha_creacion'])) : '' ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 text-xs font-bold rounded-full shadow-sm ring-1 ring-inset 
                                <?= match(strtolower($oc['estado'] ?? '')) {
                                    'pendiente' => 'bg-yellow-100 text-yellow-800 ring-yellow-300',
                                    'aprobada' => 'bg-green-100 text-green-800 ring-green-300',
                                    'rechazada' => 'bg-red-100 text-red-800 ring-red-300',
                                    default => 'bg-gray-100 text-gray-800 ring-gray-300'
                                } ?>">
                                <?= htmlspecialchars($oc['estado'] ?? '') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="index.php?c=OrdenCambio&a=detalle&id=<?= urlencode($oc['id_orden'] ?? '') ?>"
                               class="inline-flex items-center justify-center gap-1 text-blue-600 hover:text-blue-800 hover:underline font-semibold text-sm transition duration-150">
                                <i class="fas fa-eye"></i> Ver
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-6 py-6 text-center text-gray-500 italic">
                        <i class="fas fa-info-circle mr-1 text-blue-400"></i> No hay órdenes registradas.
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
