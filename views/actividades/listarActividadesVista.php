<?php 
// Variables esperadas desde ActividadControlador@index:
// $baseUrl (string)
// $actividades (array, ya procesado con 'entregables_list')
// $statusMessage (array)
include __DIR__ . '/../partials/header.php'; 
?>

<div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-700">Mis Actividades Asignadas</h1>
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
                    <th scope="col" class="px-6 py-3">Proyecto</th>
                    <th scope="col" class="px-6 py-3">Actividad</th>
                    <th scope="col" class="px-6 py-3">Entregables (Archivos)</th> <!-- NUEVA COLUMNA -->
                    <th scope="col" class="px-6 py-3">Fin Planificado</th>
                    <th scope="col" class="px-6 py-3">Estado</th>
                    <th scope="col" class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($actividades)): ?>
                    <?php foreach ($actividades as $actividad): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold"><?= htmlspecialchars($actividad['nombre_proyecto']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($actividad['nombre_actividad']) ?></td>
                            
                            <!-- CELDA PARA MOSTRAR LOS ENTREGABLES -->
                            <td class="px-6 py-4">
                                <?php if (!empty($actividad['entregables_list'])): ?>
                                    <ul class="space-y-1">
                                        <?php foreach ($actividad['entregables_list'] as $entregable): ?>
                                            <li>
                                                <a href="<?= $baseUrl . htmlspecialchars($entregable['ruta_archivo']) ?>" 
                                                   class="text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center gap-1 text-xs"
                                                   target="_blank" 
                                                   download>
                                                    <i class="fas fa-download fa-sm"></i>
                                                    <span><?= htmlspecialchars($entregable['nombre_ecs']) ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-xs text-gray-500 italic">Sin entregables.</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-4"><?= htmlspecialchars($actividad['fecha_fin_planificada'] ? date('d/m/Y', strtotime($actividad['fecha_fin_planificada'])) : 'N/A') ?></td>
                            <td class="px-6 py-4">
                                <?php
                                    $estado = htmlspecialchars($actividad['estado_actividad']);
                                    $badge_color = 'bg-gray-100 text-gray-800';
                                    switch (strtolower($estado)) {
                                        case 'pendiente': $badge_color = 'bg-yellow-100 text-yellow-800'; break;
                                        case 'en progreso': $badge_color = 'bg-blue-100 text-blue-800'; break;
                                        case 'en revisión': $badge_color = 'bg-purple-100 text-purple-800'; break;
                                        case 'completada': $badge_color = 'bg-green-100 text-green-800'; break;
                                        case 'completada con retraso': $badge_color = 'bg-orange-100 text-orange-800'; break;
                                        case 'bloqueada': $badge_color = 'bg-red-100 text-red-800'; break;
                                    }
                                ?>
                                <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs <?= $badge_color ?>">
                                    <?= $estado ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="index.php?c=Actividad&a=gestionar&id=<?= $actividad['id_actividad'] ?>" class="btn btn-primary text-xs">
                                    <i class="fas fa-cogs mr-1"></i> Gestionar
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
