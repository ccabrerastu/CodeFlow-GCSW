<?php 
// Variables esperadas desde ActividadControlador@index:
// $baseUrl (string)
// $actividades (array, ya procesado con 'entregables_list')
// $statusMessage (array)
include __DIR__ . '/../partials/header.php'; 
?>
<div class="container mx-auto mt-10 p-6 bg-white rounded-2xl shadow-2xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-extrabold text-gray-800 tracking-tight">📋 Mis Actividades Asignadas</h1>
    </div>

    <?php if (isset($statusMessage) && $statusMessage): ?>
        <div class="mb-6 px-5 py-3 text-sm rounded-lg font-medium border-l-4
            <?= $statusMessage['type'] === 'success' 
                ? 'bg-green-50 text-green-800 border-green-400' 
                : 'bg-red-50 text-red-800 border-red-400' ?>">
            <?= htmlspecialchars($statusMessage['text']) ?>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full text-sm text-gray-700">
            <thead class="text-xs text-gray-600 uppercase bg-gray-100">
                <tr>
                    <th class="px-6 py-4">📁 Proyecto</th>
                    <th class="px-6 py-4">📌 Actividad</th>
                    <th class="px-6 py-4">📂 Entregables</th>
                    <th class="px-6 py-4">📅 Fin Planificado</th>
                    <th class="px-6 py-4">📊 Estado</th>
                    <th class="px-6 py-4 text-center">⚙️ Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($actividades)): ?>
                    <?php foreach ($actividades as $actividad): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($actividad['nombre_proyecto']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($actividad['nombre_actividad']) ?></td>
                            <td class="px-6 py-4">
                                <?php if (!empty($actividad['entregables_list'])): ?>
                                    <ul class="list-disc ml-4 space-y-1 text-xs text-blue-600">
                                        <?php foreach ($actividad['entregables_list'] as $entregable): ?>
                                            <li>
                                                <a href="<?= $baseUrl . htmlspecialchars($entregable['ruta_archivo']) ?>" 
                                                   class="hover:underline flex items-center gap-1"
                                                   target="_blank" download>
                                                    <i class="fas fa-file-download fa-sm"></i>
                                                    <?= htmlspecialchars($entregable['nombre_ecs']) ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400 italic">Sin entregables</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4">
                                <?= htmlspecialchars($actividad['fecha_fin_planificada'] 
                                    ? date('d/m/Y', strtotime($actividad['fecha_fin_planificada'])) 
                                    : 'N/A') ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php
                                    $estado = htmlspecialchars($actividad['estado_actividad']);
                                    $badgeClasses = match (strtolower($estado)) {
                                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                                        'en progreso' => 'bg-blue-100 text-blue-800',
                                        'en revisión' => 'bg-purple-100 text-purple-800',
                                        'completada' => 'bg-green-100 text-green-800',
                                        'completada con retraso' => 'bg-orange-100 text-orange-800',
                                        'bloqueada' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800',
                                    };
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-medium <?= $badgeClasses ?>">
                                    <?= $estado ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="index.php?c=Actividad&a=gestionar&id=<?= $actividad['id_actividad'] ?>"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow transition">
                                    <i class="fas fa-tools"></i> Gestionar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400 text-sm italic">
                            No tienes actividades asignadas actualmente.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
