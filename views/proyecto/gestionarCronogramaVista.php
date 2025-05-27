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
                            <label for="id_fase_metodologia_form" class="form-label">Fase de Metodología:</label>
                            <select name="id_fase_metodologia" id="id_fase_metodologia_form" class="form-select" required>
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
                            <label for="id_responsable_form" class="form-label">Responsable:</label>
                            <select name="id_responsable" id="id_responsable_form" class="form-select" required>
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
                            <input type="date" 
                                name="fecha_inicio_planificada" 
                                id="fecha_inicio_planificada_act_form" 
                                class="form-input"
                                value="<?= htmlspecialchars($formDataActividad['fecha_inicio_planificada'] ?? '') ?>"
                                min="<?= htmlspecialchars($proyecto['fecha_inicio_planificada']) ?>"
                                max="<?= htmlspecialchars($proyecto['fecha_fin_planificada']) ?>">
                            <?php if (isset($formErrorsActividad['fecha_inicio_planificada'])): ?>
                                <p class="error-message"><?= htmlspecialchars($formErrorsActividad['fecha_inicio_planificada']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="fecha_fin_planificada_act_form" class="form-label">Fecha Fin Planificada:</label>
                            <input type="date" 
                                name="fecha_fin_planificada" 
                                id="fecha_fin_planificada_act_form" 
                                class="form-input"
                                value="<?= htmlspecialchars($formDataActividad['fecha_fin_planificada'] ?? '') ?>"
                                min="<?= htmlspecialchars($proyecto['fecha_inicio_planificada']) ?>"
                                max="<?= htmlspecialchars($proyecto['fecha_fin_planificada']) ?>">
                            <?php if (isset($formErrorsActividad['fecha_fin_planificada'])): ?>
                                <p class="error-message"><?= htmlspecialchars($formErrorsActividad['fecha_fin_planificada']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>


                    <div class="mb-4">
                        <label for="id_ecs_entregable_form" class="form-label">ECS Entregable Principal (Opcional):</label>
                        <select name="id_ecs_entregable" id="id_ecs_entregable_form" class="form-select">
                            <option value="">-- Ninguno --</option>
                            <?php if (!empty($ecs_del_proyecto_detallados)): ?>
                                <?php foreach ($ecs_del_proyecto_detallados as $ecs_item): ?>
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
                

                <!-- Fechas del proyecto -->
                <div class="mb-6">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Cronograma del Proyecto</h3>
                    <p class="text-gray-700">
                        <strong>Inicio planificado:</strong> <?= date('d/m/Y', strtotime($proyecto['fecha_inicio_planificada'])) ?> |
                        <strong>Fin planificado:</strong> <?= date('d/m/Y', strtotime($proyecto['fecha_fin_planificada'])) ?>
                    </p>
                </div>

                <!-- Tarjetas por fase -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php foreach ($fases_metodologia as $fase): ?>
                        <div class="border border-gray-300 rounded-lg bg-white shadow-lg overflow-hidden">
                            <div class="bg-blue-100 px-4 py-3">
                                <h4 class="text-lg font-semibold text-blue-800"><?= htmlspecialchars($fase['nombre_fase']) ?></h4>
                            </div>
                            <div class="p-4">
                                <?php
                                    $actividadesFase = array_filter($actividades, function ($actividad) use ($fase) {
                                        return $actividad['id_fase_metodologia'] == $fase['id_fase_metodologia'];
                                    });
                                ?>

                                <?php if (!empty($actividadesFase)): ?>
                                    <ul class="divide-y divide-gray-200">
                                        <?php foreach ($actividadesFase as $actividad): ?>
                                            <li class="py-3">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <p class="text-gray-800 font-medium"><?= htmlspecialchars($actividad['nombre_actividad']) ?></p>
                                                        <p class="text-sm text-gray-600">
                                                            Responsable: <?= htmlspecialchars($actividad['nombre_responsable'] ?? 'No asignado') ?>
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            Planificado: <?= date('d/m/Y', strtotime($actividad['fecha_inicio_planificada'])) ?> - 
                                                            <?= date('d/m/Y', strtotime($actividad['fecha_fin_planificada'])) ?>
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            Entrega real: 
                                                            <?= $actividad['fecha_entrega_real'] ? date('d/m/Y', strtotime($actividad['fecha_entrega_real'])) : 'Pendiente' ?>
                                                        </p>
                                                        <p class="text-sm text-gray-600">
                                                            Estado: <?= htmlspecialchars($actividad['estado_actividad']) ?>
                                                        </p>
                                                    </div>
                                                    <div class="ml-4 space-y-1">
                                                        <button 
                                                            onclick="abrirModalEditarActividad(<?= htmlspecialchars(json_encode($actividad)) ?>)"
                                                            class="text-blue-600 text-sm hover:underline flex items-center">
                                                            <i class="fas fa-edit mr-1"></i> Editar
                                                        </button>
                                                        <a href="index.php?c=Proyecto&a=eliminarActividadCronograma&id_actividad=<?= $actividad['id_actividad'] ?>&id_proyecto=<?= $proyecto['id_proyecto'] ?>&tab=cronograma"
                                                        onclick="return confirm('¿Está seguro de eliminar esta actividad?');"
                                                        class="text-red-600 text-sm hover:underline flex items-center">
                                                            <i class="fas fa-trash-alt mr-1"></i> Eliminar
                                                        </a>
                                                    </div>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-gray-500 italic">No hay actividades registradas para esta fase.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>



            <?php endif; // Fin de if ($cronograma) ?>