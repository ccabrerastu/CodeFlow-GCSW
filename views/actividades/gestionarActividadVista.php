<?php include __DIR__ . '/../partials/header.php'; ?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

<div class="container mx-auto mt-10 p-8 bg-white rounded-2xl shadow-2xl">
   <div class="mb-8">
    <a href="index.php?c=Actividad&a=index"
       class="group inline-flex items-center gap-2 text-sm text-blue-700 bg-blue-50 hover:bg-blue-100 hover:text-blue-900 font-medium px-4 py-2 rounded-lg shadow transition duration-200">
        <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform duration-200"></i>
        Volver a Mis Actividades
    </a>

    <div class="mt-6">
        <h1 class="text-4xl font-extrabold text-gray-800 flex items-center gap-2">
            🛠 Gestionar Actividad
        </h1>
        <p class="mt-1 text-lg text-gray-600 italic">
            <?= htmlspecialchars($actividad['nombre_actividad']) ?>
        </p>
    </div>
</div>

    <?php if (isset($statusMessage) && $statusMessage): ?>
        <div class="mb-6 px-5 py-3 text-sm rounded-lg font-medium border-l-4
            <?= $statusMessage['type'] === 'success' 
                ? 'bg-green-50 text-green-800 border-green-400' 
                : 'bg-red-50 text-red-800 border-red-400' ?>">
            <?= htmlspecialchars($statusMessage['text']) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- Columna: Información -->
        <div>
            <h2 class="text-xl font-bold text-gray-700 mb-4">📄 Detalles de la Actividad</h2>
            <div class="space-y-3 text-sm text-gray-700">
                <p><strong class="inline-block w-40">🔢 ID Actividad:</strong> <?= htmlspecialchars($actividad['id_actividad']) ?></p>
                <p><strong class="inline-block w-40">📝 Descripción:</strong> <?= htmlspecialchars($actividad['descripcion'] ?? 'Sin descripción.') ?></p>
                <p><strong class="inline-block w-40">📅 Inicio Planificado:</strong> <?= htmlspecialchars($actividad['fecha_inicio_planificada'] ? date('d/m/Y', strtotime($actividad['fecha_inicio_planificada'])) : 'N/A') ?></p>
                <p><strong class="inline-block w-40">📅 Fin Planificado:</strong> <?= htmlspecialchars($actividad['fecha_fin_planificada'] ? date('d/m/Y', strtotime($actividad['fecha_fin_planificada'])) : 'N/A') ?></p>
                <p><strong class="inline-block w-40">📬 Entrega Real:</strong> <?= htmlspecialchars($actividad['fecha_entrega_real'] ? date('d/m/Y H:i', strtotime($actividad['fecha_entrega_real'])) : 'Pendiente') ?></p>
            </div>

            <h3 class="text-lg font-bold mt-8 mb-2">📌 Actualizar Estado</h3>
            <form action="index.php?c=Actividad&a=actualizarEstado" method="POST" class="mt-2">
                <input type="hidden" name="id_actividad" value="<?= htmlspecialchars($actividad['id_actividad']) ?>">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <select name="estado_actividad" class="block w-full sm:w-1/2 rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="Pendiente" <?= $actividad['estado_actividad'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                        <option value="En Progreso" <?= $actividad['estado_actividad'] == 'En Progreso' ? 'selected' : '' ?>>En Progreso</option>
                    </select>
                    <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5 shadow">
                        <i class="fas fa-sync-alt mr-1"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>

        <!-- Columna: Entregables -->
        <div>
            <h2 class="text-xl font-bold text-gray-700 mb-4">📦 Entregables (ECS)</h2>
            <?php if (!empty($entregables)): ?>
                <div class="space-y-5">
                    <?php foreach ($entregables as $ecs): ?>
                        <div class="bg-gray-50 p-5 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="font-semibold text-md text-gray-800"><?= htmlspecialchars($ecs['nombre_ecs']) ?></h4>
                            <p class="text-xs text-gray-500 mb-2">🔄 Versión: <?= htmlspecialchars($ecs['version_actual']) ?></p>
                            
                            <?php if (!empty($ecs['ruta_archivo'])): ?>
                                <div class="text-green-600 mb-2 text-sm flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i> Entregado: 
                                    <a href="<?= $baseUrl . htmlspecialchars($ecs['ruta_archivo']) ?>" target="_blank" class="underline font-medium">Ver archivo</a>
                                </div>
                            <?php else: ?>
                                <form action="index.php?c=Actividad&a=subirEntregable" method="POST" enctype="multipart/form-data" class="space-y-2">
                                    <input type="hidden" name="id_actividad" value="<?= htmlspecialchars($actividad['id_actividad']) ?>">
                                    <input type="hidden" name="id_ecs" value="<?= htmlspecialchars($ecs['id_ecs']) ?>">
                                    <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($actividad['id_proyecto']) ?>">

                                    <label for="archivo_entregable_<?= $ecs['id_ecs'] ?>" class="text-sm font-medium text-gray-600">📤 Subir Archivo:</label>
                                    <div class="flex items-center gap-2">
                                        <input type="file" name="archivo_entregable" id="archivo_entregable_<?= $ecs['id_ecs'] ?>"
                                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                                               file:rounded-full file:border-0 file:font-semibold file:bg-blue-50 file:text-blue-700
                                               hover:file:bg-blue-100" required>
                                        <button type="submit" class="text-white bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-sm shadow flex items-center gap-1">
                                            <i class="fas fa-upload"></i> Subir
                                        </button>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="italic text-gray-500 mt-3">Esta actividad no tiene entregables configurados.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
