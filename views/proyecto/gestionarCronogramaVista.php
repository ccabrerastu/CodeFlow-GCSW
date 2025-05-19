<h2 class="section-title">Cronograma del Proyecto</h2>
            
            <?php if (isset($formErrorsActividad['general_actividad'])): ?>
                <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    <?= htmlspecialchars($formErrorsActividad['general_actividad']) ?>
                </div>
            <?php endif; ?>

            <?php if (!$cronograma || !isset($cronograma['id_cronograma'])): ?>
                <div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg" role="alert">
                    Este proyecto aún no tiene un cronograma.
                    <a href="index.php?c=Proyecto&a=crearCronogramaParaProyecto&id_proyecto=<?= htmlspecialchars($proyecto['id_proyecto']) ?>" class="font-bold underline hover:text-blue-800 ml-2">Crear Cronograma Ahora</a>
                </div>
            <?php else: ?>
                <p class="mb-2 text-sm text-gray-600">Cronograma ID: <?= htmlspecialchars($cronograma['id_cronograma']) ?> - <?= htmlspecialchars($cronograma['descripcion']) ?></p>
                
                <form action="<?= $baseUrl ?>index.php?c=Proyecto&a=agregarActividadCronograma" method="POST" class="mb-6 p-4 border rounded-md bg-gray-50 shadow">
                    <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">
                    <input type="hidden" name="id_cronograma" value="<?= htmlspecialchars($cronograma['id_cronograma']) ?>">
                    
                    <h3 class="text-xl font-semibold text-gray-700 mb-4">Agregar Nueva Actividad</h3>
                    
                    <div class="mb-4">
                        <label for="nombre_actividad" class="form-label">Nombre de la Actividad:</label>
                        <input type="text" name="nombre_actividad" id="nombre_actividad" class="form-input" 
                               value="<?= htmlspecialchars($formDataActividad['nombre_actividad'] ?? '') ?>" required>
                        <?php if (isset($formErrorsActividad['nombre_actividad'])): ?>
                            <p class="error-message"><?= htmlspecialchars($formErrorsActividad['nombre_actividad']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="descripcion_actividad" class="form-label">Descripción (Opcional):</label>
                        <textarea name="descripcion_actividad" id="descripcion_actividad" rows="3" 
                                  class="form-textarea"><?= htmlspecialchars($formDataActividad['descripcion_actividad'] ?? '') ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="id_fase_metodologia_form" class="form-label">Fase de Metodología (Opcional):</label>
                            <select name="id_fase_metodologia" id="id_fase_metodologia_form" class="form-select">
                                <option value="">-- Ninguna --</option>
                                <?php if (!empty($fases_metodologia)): ?>
                                    <?php foreach ($fases_metodologia as $fase_met): ?>
                                        <option value="<?= htmlspecialchars($fase_met['id_fase_metodologia']) ?>"
                                            <?= (isset($formDataActividad['id_fase_metodologia']) && $formDataActividad['id_fase_metodologia'] == $fase_met['id_fase_metodologia']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($fase_met['nombre_fase']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                     <option value="" disabled>No hay fases definidas para la metodología del proyecto.</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label for="id_responsable_form" class="form-label">Responsable (Opcional):</label>
                            <select name="id_responsable" id="id_responsable_form" class="form-select">
                                <option value="">-- Ninguno --</option>
                                <?php if (!empty($miembros_equipo)): ?>
                                    <?php foreach ($miembros_equipo as $miembro): ?>
                                        <option value="<?= htmlspecialchars($miembro['id_usuario']) ?>"
                                            <?= (isset($formDataActividad['id_responsable']) && $formDataActividad['id_responsable'] == $miembro['id_usuario']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($miembro['nombre_completo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>No hay miembros en el equipo del proyecto.</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="fecha_inicio_planificada_act_form" class="form-label">Fecha Inicio Planificada:</label>
                            <input type="date" name="fecha_inicio_planificada" id="fecha_inicio_planificada_act_form" class="form-input"
                                   value="<?= htmlspecialchars($formDataActividad['fecha_inicio_planificada'] ?? '') ?>">
                            <?php if (isset($formErrorsActividad['fecha_inicio_planificada'])): ?>
                                <p class="error-message"><?= htmlspecialchars($formErrorsActividad['fecha_inicio_planificada']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="fecha_fin_planificada_act_form" class="form-label">Fecha Fin Planificada:</label>
                            <input type="date" name="fecha_fin_planificada" id="fecha_fin_planificada_act_form" class="form-input"
                                   value="<?= htmlspecialchars($formDataActividad['fecha_fin_planificada'] ?? '') ?>">
                            <?php if (isset($formErrorsActividad['fecha_fin_planificada'])): ?>
                                <p class="error-message"><?= htmlspecialchars($formErrorsActividad['fecha_fin_planificada']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="id_ecs_entregable_form" class="form-label">ECS Entregable Principal (Opcional):</label>
                        <select name="id_ecs_entregable" id="id_ecs_entregable_form" class="form-select">
                            <option value="">-- Ninguno --</option>
                            <?php if (!empty($ecs_definidos)): ?>
                                <?php foreach ($ecs_definidos as $ecs_item): ?>
                                    <option value="<?= htmlspecialchars($ecs_item['id_ecs']) ?>"
                                        <?= (isset($formDataActividad['id_ecs_entregable']) && $formDataActividad['id_ecs_entregable'] == $ecs_item['id_ecs']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ecs_item['nombre_ecs']) ?> (ID: <?= htmlspecialchars($ecs_item['id_ecs']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay ECS definidos para este proyecto.</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">
                        <i class="fas fa-plus mr-1"></i> Agregar Actividad
                    </button>
                </form>

                <h3 class="text-xl font-semibold text-gray-700 mb-4 mt-8">Actividades del Cronograma</h3>
                <?php if (!empty($actividades)): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
                            <thead class="bg-gray-100 border-b border-gray-300">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Nombre</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Fase</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Responsable</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Inicio Plan.</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Fin Plan.</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Entrega Real</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Estado</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $rowIndex = 0; foreach ($actividades as $actividad): ?>
                                    <tr class="border-b border-gray-200 hover:bg-gray-50 <?= ($rowIndex % 2 === 0) ? 'bg-white' : 'bg-gray-50'; ?>">
                                        <td class="px-4 py-2"><?= htmlspecialchars($actividad['nombre_actividad']) ?></td>
                                        <td class="px-4 py-2"><?= htmlspecialchars($actividad['nombre_fase'] ?? 'N/A') ?></td>
                                        <td class="px-4 py-2"><?= htmlspecialchars($actividad['nombre_responsable'] ?? 'No asignado') ?></td>
                                        <td class="px-4 py-2"><?= htmlspecialchars($actividad['fecha_inicio_planificada'] ? date('d/m/Y', strtotime($actividad['fecha_inicio_planificada'])) : 'N/A') ?></td>
                                        <td class="px-4 py-2"><?= htmlspecialchars($actividad['fecha_fin_planificada'] ? date('d/m/Y', strtotime($actividad['fecha_fin_planificada'])) : 'N/A') ?></td>
                                        <td class="px-4 py-2"><?= htmlspecialchars($actividad['fecha_entrega_real'] ? date('d/m/Y', strtotime($actividad['fecha_entrega_real'])) : 'Pendiente') ?></td>
                                        <td class="px-4 py-2"><?= htmlspecialchars($actividad['estado_actividad']) ?></td>
                                        <td class="px-4 py-2 space-x-1">
                                            <button type="button" 
                                                onclick="abrirModalEditarActividad(<?= htmlspecialchars(json_encode($actividad)) ?>)"
                                                class="btn btn-edit text-xs">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <a href="index.php?c=Proyecto&a=eliminarActividadCronograma&id_actividad=<?= $actividad['id_actividad'] ?>&id_proyecto=<?= $proyecto['id_proyecto'] ?>&tab=cronograma" 
                                               class="btn btn-delete text-xs" 
                                               onclick="return confirm('¿Está seguro de eliminar esta actividad?');">
                                                <i class="fas fa-trash-alt"></i> Eliminar
                                            </a>
                                        </td>
                                    </tr>
                                <?php $rowIndex++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">No hay actividades definidas para este cronograma.</p>
                <?php endif; ?>
            <?php endif; // Fin de if ($cronograma) ?>