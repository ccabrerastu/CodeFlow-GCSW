<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-700">Mis Actividades Asignadas</h1>
    </div>

    <?php if (isset($statusMessage) && $statusMessage): ?>
        <div class="p-3 mb-4 text-sm <?= $statusMessage['type'] === 'success' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' ?> rounded-lg" role="alert">
            <?= htmlspecialchars($statusMessage['text']) ?>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">Proyecto</th>
                    <th scope="col" class="px-6 py-3">Nombre Actividad</th>
                    <th scope="col" class="px-6 py-3">Fase</th>
                    <th scope="col" class="px-6 py-3">Fin Planificado</th>
                    <th scope="col" class="px-6 py-3">Estado</th>
                    <th scope="col" class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($actividades)): ?>
                    <?php foreach ($actividades as $actividad): ?>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-900"><?= htmlspecialchars($actividad['nombre_proyecto']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($actividad['nombre_actividad']) ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($actividad['nombre_fase'] ?? 'N/A') ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($actividad['fecha_fin_planificada'] ? date('d/m/Y', strtotime($actividad['fecha_fin_planificada'])) : 'N/A') ?></td>
                            <td class="px-6 py-4"><?= htmlspecialchars($actividad['estado_actividad']) ?></td>
                            <td class="px-6 py-4">
                                <a href="index.php?c=Actividad&a=gestionar&id=<?= $actividad['id_actividad'] ?>" class="btn btn-primary text-xs">
                                    <i class="fas fa-cogs mr-1"></i> Gestionar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No tienes actividades asignadas actualmente.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
