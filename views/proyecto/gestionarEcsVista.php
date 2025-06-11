<?php
$id_proyecto_actual = $proyecto['id_proyecto'] ?? null;
$nombre_metodologia_proyecto = $proyecto['nombre_metodologia'] ?? 'Metodología no definida';

?>

<h2 class="section-title">Elementos de Configuración (ECS)</h2>

<?php if (isset($formErrorsECS['general_ecs'])): ?>
    <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
        <?= htmlspecialchars($formErrorsECS['general_ecs']) ?>
    </div>
<?php endif; ?>

<form action="<?= $baseUrl ?>index.php?c=Proyecto&a=guardarSeleccionECSProyecto" method="POST" class="mb-8 p-6 border border-gray-200 rounded-lg bg-white shadow-sm">
    <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($id_proyecto_actual) ?>">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">
        Seleccionar ECS desde Metodología: <span class="font-medium text-indigo-600"><?= htmlspecialchars($nombre_metodologia_proyecto) ?></span>
    </h3>

    <?php if (!empty($fases_con_ecs_plantilla)): ?>
        <div class="space-y-6">
           <?php foreach ($fases_con_ecs_plantilla as $fase_data): ?>
                <section class="bg-indigo-50 p-4 rounded-md border border-indigo-200">
                    <h4 class="text-lg font-semibold text-indigo-700 mb-3 flex items-center">
                        <i class="fas fa-layer-group mr-2"></i>Fase: <?= htmlspecialchars($fase_data['nombre_fase']) ?>
                    </h4>
                    <?php if (!empty($fase_data['elementos'])): ?>
                        <div class="space-y-3 pl-2">
                            <?php foreach ($fase_data['elementos'] as $ecs_plantilla): ?>
                                <div class="p-3 rounded-lg bg-white border border-gray-200 hover:border-indigo-300 transition-all">
                                    <label class="flex items-center justify-between gap-4 cursor-pointer">
                                        <div class="flex items-center gap-3">
                                            <input 
                                                type="checkbox" 
                                                name="elementos_seleccionados[]" 
                                                value="<?= htmlspecialchars($ecs_plantilla['id_ec_fase_met']) ?>"
                                                class="form-checkbox h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition"
                                                <?= (isset($ecs_seleccionados_ids) && is_array($ecs_seleccionados_ids) && in_array($ecs_plantilla['id_ec_fase_met'], $ecs_seleccionados_ids)) ? 'checked' : '' ?>
                                            >
                                            <div>
                                                <span class="font-medium text-gray-800"><?= htmlspecialchars($ecs_plantilla['nombre_ecs']) ?></span>
                                                <span class="text-sm text-gray-500">(<?= htmlspecialchars($ecs_plantilla['tipo_ecs'] ?? 'Genérico') ?>)</span>
                                                <?php if(!empty($ecs_plantilla['descripcion_en_fase'])): ?>
                                                    <p class="text-xs text-gray-500 italic mt-1"><?= htmlspecialchars($ecs_plantilla['descripcion_en_fase']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm">
                                            <span class="bg-gray-200 text-gray-700 px-2 py-1 rounded-full text-xs font-semibold">
                                                v<?= htmlspecialchars($ecs_plantilla['version_actual']) ?>
                                                
                                            </span>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                                <?= htmlspecialchars($ecs_plantilla['estado_ecs']) ?>
                                            </span>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="italic text-gray-500 mt-1 pl-2 text-sm">No hay ECS predefinidos para esta fase en la metodología.</p>
                    <?php endif; ?>

                </section>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary mt-6">
            <i class="fas fa-check-double mr-1"></i> Guardar Selección de ECS
        </button>
    <?php else: ?>
        <p class="text-gray-500 italic">La metodología seleccionada para este proyecto no tiene fases o ECS predefinidos, o no se pudo cargar la información de la metodología.</p>
    <?php endif; ?>
</form>

<hr class="my-8 border-gray-300">

<!-- 2. Sección de ECS Personalizado -->
<div class="mb-6 p-6 border border-gray-200 rounded-lg bg-white shadow-sm">
    <div class="flex justify-between items-center">
        <h3 class="text-xl font-semibold text-gray-800">ECS Personalizados</h3>
        <!-- Botón para mostrar el formulario -->
        <button id="show-custom-ecs-form-btn" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i>Agregar ECS Personalizado
        </button>
    </div>
    
    <!-- El formulario está aquí, pero oculto por defecto -->
    <form id="custom-ecs-form-container" action="<?= $baseUrl ?>index.php?c=Proyecto&a=agregarECSProyecto" method="POST" class="mt-6 border-t pt-6 hidden">
        <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">
        
        <div class="mb-4">
            <label for="nombre_ecs_personalizado" class="form-label">Nombre del ECS:</label>
            <input type="text" name="nombre_ecs" id="nombre_ecs_personalizado" class="form-input" 
                   value="<?= htmlspecialchars($formDataECS['nombre_ecs'] ?? '') ?>" required>
            <?php if (isset($formErrorsECS['nombre_ecs'])): ?>
                <p class="error-message"><?= htmlspecialchars($formErrorsECS['nombre_ecs']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="tipo_ecs_personalizado" class="form-label">Tipo de ECS:</label>
            <input type="text" name="tipo_ecs" id="tipo_ecs_personalizado" class="form-input" 
                   placeholder="Ej: Documento ERS, Diagrama de Clases, Módulo Login"
                   value="<?= htmlspecialchars($formDataECS['tipo_ecs'] ?? '') ?>">
             <?php if (isset($formErrorsECS['tipo_ecs'])): ?>
                <p class="error-message"><?= htmlspecialchars($formErrorsECS['tipo_ecs']) ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="descripcion_ecs_personalizado" class="form-label">Descripción del ECS (Opcional):</label>
            <textarea name="descripcion_ecs" id="descripcion_ecs_personalizado" rows="3" 
                      class="form-textarea"><?= htmlspecialchars($formDataECS['descripcion_ecs'] ?? '') ?></textarea>
        </div>

        <div class="mb-4">
            <label for="id_fase_metodologia_personalizado" class="form-label">Fase de Metodología (Opcional):</label>
            <select name="id_fase_metodologia" id="id_fase_metodologia_personalizado" class="form-select">
                <option value="">Seleccione una fase (si aplica)</option>
                <?php if (!empty($fases_metodologia_cronograma)): ?>
                    <?php foreach ($fases_metodologia_cronograma as $fase): ?>
                        <option value="<?= htmlspecialchars($fase['id_fase_metodologia']) ?>"
                            <?= (isset($formDataECS['id_fase_metodologia']) && $formDataECS['id_fase_metodologia'] == $fase['id_fase_metodologia']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($fase['nombre_fase']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <?php if (isset($formErrorsECS['id_fase_metodologia'])): ?>
                <p class="error-message"><?= htmlspecialchars($formErrorsECS['id_fase_metodologia']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="flex items-center gap-4 mt-6">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save mr-1"></i> Guardar ECS
            </button>
            <button type="button" id="hide-custom-ecs-form-btn" class="btn btn-secondary">
                Cancelar
            </button>
        </div>
    </form>
</div>

<!-- 3. Lista de ECS Activos en el Proyecto (DISEÑO DE TARJETAS) -->
<h3 class="text-xl font-semibold text-gray-700 mb-4 mt-8">ECS Activos en el Proyecto</h3>
<?php if (!empty($ecs_del_proyecto_detallados)): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($ecs_del_proyecto_detallados as $ecs_p): 
            // Lógica para los colores de los badges de estado
            $estado = htmlspecialchars($ecs_p['estado'] ?? 'N/A');
            $badge_color = 'bg-gray-200 text-gray-800'; // Default
            switch (strtolower($estado)) {
                case 'definido':
                    $badge_color = 'bg-blue-100 text-blue-800';
                    break;
                case 'en progreso':
                    $badge_color = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'completado':
                    $badge_color = 'bg-green-100 text-green-800';
                    break;
                case 'bloqueado':
                    $badge_color = 'bg-red-100 text-red-800';
                    break;
            }
        ?>
            <!-- Tarjeta individual para un ECS -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 flex flex-col hover:shadow-lg transition-shadow duration-300">
                <!-- Cabecera de la Tarjeta -->
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div class="flex-grow">
                            <h4 class="font-bold text-lg text-gray-800"><?= htmlspecialchars($ecs_p['nombre_ecs']) ?></h4>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($ecs_p['tipo_ecs'] ?? 'N/A') ?></p>
                        </div>
                        <span class="text-xs font-mono text-gray-400 ml-2">#<?= htmlspecialchars($ecs_p['id_ecs_proyecto']) ?></span>
                    </div>
                </div>

                <!-- Cuerpo de la Tarjeta -->
                <div class="p-4 flex-grow">
                    <div class="text-sm text-gray-700 mb-3">
                        <span class="font-semibold">Fase de Origen:</span>
                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md"><?= htmlspecialchars($ecs_p['nombre_fase'] ?? 'Personalizado') ?></span>
                    </div>
                    <div class="flex items-center gap-2 mb-3">
                        <span class="font-semibold text-sm">Estado:</span>
                        <span class="px-2 py-1 font-semibold leading-tight rounded-full text-xs <?= $badge_color ?>">
                            <?= $estado ?>
                        </span>
                    </div>
                     <?php if (!empty($ecs_p['comentario'])): ?>
                        <p class="text-sm text-gray-600 bg-gray-50 p-2 rounded-md border italic">
                            "<?= htmlspecialchars($ecs_p['comentario']) ?>"
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Pie de la Tarjeta (Acciones) -->
                <div class="border-t border-gray-200 bg-gray-50 px-4 py-3 mt-auto flex justify-end space-x-3">
                    <button class="btn btn-edit text-xs" title="Editar Estado/Comentario">
                        <i class="fas fa-pencil-alt"></i>
                        <span>Editar</span>
                    </button>
                    <a href="index.php?c=Proyecto&a=eliminarECSDelProyecto&id_ecs_proyecto=<?= $ecs_p['id_ecs_proyecto'] ?>&id_proyecto=<?= $id_proyecto_actual ?>&tab=ecs" 
                       class="btn btn-delete text-xs"
                       title="Quitar ECS del Proyecto"
                       onclick="return confirm('¿Está seguro de quitar este ECS del proyecto?');">
                        <i class="fas fa-trash-alt"></i>
                        <span>Quitar</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <!-- Mensaje cuando no hay ECS activos -->
    <div class="mt-6 text-center py-8 px-4 border-2 border-dashed border-gray-300 rounded-lg">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
            <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No hay ECS activos</h3>
        <p class="mt-1 text-sm text-gray-500">Seleccione elementos de la metodología o agregue uno personalizado para comenzar.</p>
    </div>
<?php endif; ?>
