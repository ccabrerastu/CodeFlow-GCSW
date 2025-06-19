<section class="p-6 bg-white rounded-xl shadow-lg">
    <h2 class="text-3xl font-bold text-indigo-800 mb-6">📅 Cronograma del Proyecto</h2>

    <?php if (isset($formErrorsActividad['general_actividad'])): ?>
        <div class="p-4 mb-6 text-sm text-red-700 bg-red-100 border border-red-200 rounded-lg shadow">
            <?= htmlspecialchars($formErrorsActividad['general_actividad']) ?>
        </div>
    <?php endif; ?>

    <?php if (!$cronograma || !isset($cronograma['id_cronograma'])): ?>
        <div class="p-4 mb-6 text-blue-800 bg-blue-100 border border-blue-200 rounded-lg shadow flex items-center justify-between">
            <span>Este proyecto aún no tiene un cronograma.</span>
            <a href="index.php?c=Proyecto&a=crearCronogramaParaProyecto&id_proyecto=<?= htmlspecialchars($proyecto['id_proyecto']) ?>" class="inline-block text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-md font-medium shadow-md transition">Crear Cronograma</a>
        </div>
    <?php else: ?>

        <!-- Datos del Cronograma -->
        <p class="text-sm text-gray-600 mb-2">
            <span class="font-medium">ID Cronograma:</span> <?= htmlspecialchars($cronograma['id_cronograma']) ?> |
            <span class="italic"><?= htmlspecialchars($cronograma['descripcion']) ?></span>
        </p>

        <!-- Formulario de nueva actividad -->
        <form action="<?= $baseUrl ?>index.php?c=Proyecto&a=agregarActividadCronograma" method="POST" class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-8 shadow-md space-y-4">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">➕ Agregar Nueva Actividad</h3>

            <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">
            <input type="hidden" name="id_cronograma" value="<?= htmlspecialchars($cronograma['id_cronograma']) ?>">

            <!-- Campo nombre actividad -->
            <div>
                <label for="nombre_actividad" class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Actividad:</label>
                <input type="text" name="nombre_actividad" id="nombre_actividad" required
                    value="<?= htmlspecialchars($formDataActividad['nombre_actividad'] ?? '') ?>"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <?php if (isset($formErrorsActividad['nombre_actividad'])): ?>
                    <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrorsActividad['nombre_actividad']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Descripción -->
            <div>
                <label for="descripcion_actividad" class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional):</label>
                <textarea name="descripcion_actividad" id="descripcion_actividad" rows="3"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"><?= htmlspecialchars($formDataActividad['descripcion_actividad'] ?? '') ?></textarea>
            </div>

            <!-- Fase y responsable -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fase de Metodología:</label>
                    <select name="id_fase_metodologia" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Ninguna --</option>
                        <?php foreach ($fases_metodologia as $fase_met): ?>
                            <option value="<?= htmlspecialchars($fase_met['id_fase_metodologia']) ?>" <?= (isset($formDataActividad['id_fase_metodologia']) && $formDataActividad['id_fase_metodologia'] == $fase_met['id_fase_metodologia']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($fase_met['nombre_fase']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Responsable:</label>
                    <select name="id_responsable" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Ninguno --</option>
                        <?php foreach ($miembros_equipo as $miembro): ?>
                            <option value="<?= htmlspecialchars($miembro['id_usuario']) ?>" <?= (isset($formDataActividad['id_responsable']) && $formDataActividad['id_responsable'] == $miembro['id_usuario']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($miembro['nombre_completo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Fechas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio Planificada:</label>
                    <input type="date" name="fecha_inicio_planificada"
                        value="<?= htmlspecialchars($formDataActividad['fecha_inicio_planificada'] ?? '') ?>"
                        min="<?= htmlspecialchars($proyecto['fecha_inicio_planificada']) ?>"
                        max="<?= htmlspecialchars($proyecto['fecha_fin_planificada']) ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php if (isset($formErrorsActividad['fecha_inicio_planificada'])): ?>
                        <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrorsActividad['fecha_inicio_planificada']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin Planificada:</label>
                    <input type="date" name="fecha_fin_planificada"
                        value="<?= htmlspecialchars($formDataActividad['fecha_fin_planificada'] ?? '') ?>"
                        min="<?= htmlspecialchars($proyecto['fecha_inicio_planificada']) ?>"
                        max="<?= htmlspecialchars($proyecto['fecha_fin_planificada']) ?>"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <?php if (isset($formErrorsActividad['fecha_fin_planificada'])): ?>
                        <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrorsActividad['fecha_fin_planificada']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ECS entregable -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">ECS Entregable Principal (opcional):</label>
                <select name="id_ecs_entregable" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Ninguno --</option>
                    <?php foreach ($ecs_del_proyecto_detallados as $ecs_item): ?>
                        <option value="<?= htmlspecialchars($ecs_item['id_ecs']) ?>" <?= (isset($formDataActividad['id_ecs_entregable']) && $formDataActividad['id_ecs_entregable'] == $ecs_item['id_ecs']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ecs_item['nombre_ecs']) ?> (ID: <?= htmlspecialchars($ecs_item['id_ecs']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Botón -->
            <div class="pt-2">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition flex items-center gap-2">
                    <i class="fas fa-plus"></i> Agregar Actividad
                </button>
            </div>
        </form>

<!-- Fechas generales del proyecto -->
<div class="mb-8 p-6 bg-gradient-to-r from-indigo-100 to-white border-l-4 border-indigo-400 rounded-xl shadow-lg">
    <div class="flex items-center mb-2">
        <i class="fas fa-calendar-alt text-indigo-600 text-xl mr-2"></i>
        <h3 class="text-xl font-semibold text-indigo-800">Fechas del Proyecto</h3>
    </div>
    <div class="text-gray-700 pl-7">
        <p class="mb-1">
            <span class="font-semibold">📅 Inicio planificado:</span>
            <?= date('d/m/Y', strtotime($proyecto['fecha_inicio_planificada'])) ?>
        </p>
        <p>
            <span class="font-semibold">🏁 Fin planificado:</span>
            <?= date('d/m/Y', strtotime($proyecto['fecha_fin_planificada'])) ?>
        </p>
    </div>
</div>

        <!-- Actividades por fase -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php foreach ($fases_metodologia as $fase): ?>
                <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4">
                    <h4 class="text-lg font-semibold text-indigo-700 mb-3"><?= htmlspecialchars($fase['nombre_fase']) ?></h4>
                    <?php
                        $actividadesFase = array_filter($actividades, function ($a) use ($fase) {
                            return $a['id_fase_metodologia'] == $fase['id_fase_metodologia'];
                        });
                    ?>
                    <?php if (!empty($actividadesFase)): ?>
                        <ul class="divide-y divide-gray-200">
                            <?php foreach ($actividadesFase as $actividad): ?>
                                <li class="py-3">
                                    <p class="text-gray-800 font-semibold"><?= htmlspecialchars($actividad['nombre_actividad']) ?></p>
                                    <p class="text-sm text-gray-600">👤 Responsable: <?= htmlspecialchars($actividad['nombre_responsable'] ?? 'No asignado') ?></p>
                                    <p class="text-sm text-gray-600">📆 Planificado: <?= date('d/m/Y', strtotime($actividad['fecha_inicio_planificada'])) ?> - <?= date('d/m/Y', strtotime($actividad['fecha_fin_planificada'])) ?></p>
                                    <p class="text-sm text-gray-600">📤 Entrega real: <?= $actividad['fecha_entrega_real'] ? date('d/m/Y', strtotime($actividad['fecha_entrega_real'])) : 'Pendiente' ?></p>
                                    <p class="text-sm text-gray-600">📌 Estado: <?= htmlspecialchars($actividad['estado_actividad']) ?></p>
                                    <div class="mt-2 flex gap-4">
                                        <button onclick="abrirModalEditarActividad(<?= htmlspecialchars(json_encode($actividad)) ?>)" class="text-sm text-indigo-600 hover:underline flex items-center gap-1">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <a href="index.php?c=Proyecto&a=eliminarActividadCronograma&id_actividad=<?= $actividad['id_actividad'] ?>&id_proyecto=<?= $proyecto['id_proyecto'] ?>&tab=cronograma" onclick="return confirm('¿Está seguro de eliminar esta actividad?');" class="text-sm text-red-600 hover:underline flex items-center gap-1">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-sm text-gray-500 italic">No hay actividades en esta fase.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
