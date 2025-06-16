<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
    <div class="mb-6">
        <a href="index.php?c=Actividad&a=index" class="text-blue-600 hover:underline">&larr; Volver a Mis Actividades</a>
        <h1 class="text-3xl font-bold text-gray-800 mt-2">Gestionar Actividad</h1>
        <p class="text-lg text-gray-600"><?= htmlspecialchars($actividad['nombre_actividad']) ?></p>
    </div>

    <?php if (isset($statusMessage) && $statusMessage): ?>
        <div class="p-3 mb-4 text-sm <?= $statusMessage['type'] === 'success' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' ?> rounded-lg" role="alert">
            <?= htmlspecialchars($statusMessage['text']) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Columna de Información -->
        <div>
            <h2 class="section-title">Detalles de la Actividad</h2>
            <div class="space-y-3 text-sm">
                <p><strong class="w-32 inline-block">ID Actividad:</strong> <?= htmlspecialchars($actividad['id_actividad']) ?></p>
                <p><strong class="w-32 inline-block">Descripción:</strong> <?= htmlspecialchars($actividad['descripcion'] ?? 'Sin descripción.') ?></p>
                <p><strong class="w-32 inline-block">Fecha Inicio Plan.:</strong> <?= htmlspecialchars($actividad['fecha_inicio_planificada'] ? date('d/m/Y', strtotime($actividad['fecha_inicio_planificada'])) : 'N/A') ?></p>
                <p><strong class="w-32 inline-block">Fecha Fin Plan.:</strong> <?= htmlspecialchars($actividad['fecha_fin_planificada'] ? date('d/m/Y', strtotime($actividad['fecha_fin_planificada'])) : 'N/A') ?></p>
                <p><strong class="w-32 inline-block">Fecha Entrega Real:</strong> <?= htmlspecialchars($actividad['fecha_entrega_real'] ? date('d/m/Y H:i', strtotime($actividad['fecha_entrega_real'])) : 'Pendiente') ?></p>
            </div>

            <h3 class="font-bold text-lg mb-2 mt-6">Actualizar Estado</h3>
            <form action="index.php?c=Actividad&a=actualizarEstado" method="POST">
                <input type="hidden" name="id_actividad" value="<?= htmlspecialchars($actividad['id_actividad']) ?>">
                <div class="flex items-center gap-4">
                    <select name="estado_actividad" class="form-select w-full md:w-1/2">
                        <option value="Pendiente" <?= $actividad['estado_actividad'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="En Progreso" <?= $actividad['estado_actividad'] == 'En Progreso' ? 'selected' : '' ?>>En Progreso</option>
                        <!-- Los usuarios no pueden marcar como Completada o Bloqueada desde aquí -->
                    </select>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>

        <!-- Columna de Entregables -->
        <div>
            <h2 class="section-title">Entregables (ECS)</h2>
            <?php if (!empty($entregables)): ?>
                <div class="space-y-4">
                    <?php foreach ($entregables as $ecs): ?>
                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <h4 class="font-semibold text-md text-gray-800"><?= htmlspecialchars($ecs['nombre_ecs']) ?></h4>
                            <p class="text-xs text-gray-500 mb-3">Versión: <?= htmlspecialchars($ecs['version_actual']) ?></p>
                            
                            <?php if (!empty($ecs['ruta_archivo'])): ?>
                                <div class="text-green-600 mb-3">
                                    <i class="fas fa-check-circle mr-2"></i>Entregado: 
                                    <a href="<?= $baseUrl . htmlspecialchars($ecs['ruta_archivo']) ?>" target="_blank" class="font-medium underline">Ver archivo</a>
                                </div>
                            <?php else: ?>
                                <form action="index.php?c=Actividad&a=subirEntregable" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="id_actividad" value="<?= htmlspecialchars($actividad['id_actividad']) ?>">
                                    <input type="hidden" name="id_ecs" value="<?= htmlspecialchars($ecs['id_ecs']) ?>">
                                    <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($actividad['id_proyecto']) ?>">

                                    <label for="archivo_entregable_<?= $ecs['id_ecs'] ?>" class="form-label">Subir Archivo:</label>
                                    <div class="flex items-center gap-2">
                                        <input type="file" name="archivo_entregable" id="archivo_entregable_<?= $ecs['id_ecs'] ?>" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                                        <button type="submit" class="btn btn-primary whitespace-nowrap"><i class="fas fa-upload"></i></button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="italic text-gray-500">Esta actividad no tiene Elementos de Configuración asociados como entregables.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
