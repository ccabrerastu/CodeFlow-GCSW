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
                        <ul class="space-y-2 ml-3">
                            <?php foreach ($fase_data['elementos'] as $ecs_plantilla): ?>
                                <li>
                                    <label class="flex items-center gap-3 text-gray-700 hover:text-indigo-600 transition-all cursor-pointer">
                                        <input 
                                            type="checkbox" 
                                            name="elementos_seleccionados[]" 
                                            value="<?= htmlspecialchars($ecs_plantilla['id_ec_fase_met']) ?>"
                                            class="form-checkbox h-5 w-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 transition"
                                            <?= (isset($ecs_seleccionados_ids) && is_array($ecs_seleccionados_ids) && in_array($ecs_plantilla['id_ec_fase_met'], $ecs_seleccionados_ids)) ? 'checked' : '' ?>
                                        >
                                        <span class="text-base">
                                            <?= htmlspecialchars($ecs_plantilla['nombre_ecs']) ?>
                                            <span class="text-sm text-gray-500">(<?= htmlspecialchars($ecs_plantilla['tipo_ecs'] ?? 'Genérico') ?>)</span>
                                            <?php if(!empty($ecs_plantilla['descripcion_en_fase'])): ?>
                                                <span class="block text-xs text-gray-500 italic ml-1">- <?= htmlspecialchars($ecs_plantilla['descripcion_en_fase']) ?></span>
                                            <?php endif; ?>
                                        </span>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="italic text-gray-500 mt-1 text-sm">No hay ECS predefinidos para esta fase en la metodología.</p>
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

<form action="<?= $baseUrl ?>index.php?c=Proyecto&a=agregarECSProyecto" method="POST" class="mb-6 p-6 border border-gray-200 rounded-lg bg-white shadow-sm">
    <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($id_proyecto_actual) ?>">
    <h3 class="text-xl font-semibold text-gray-800 mb-4">Agregar Nuevo ECS Personalizado</h3>
    
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
        <label for="id_fase_metodologia" class="form-label">Fase de la Metodología:</label>
        <select name="id_fase_metodologia" id="id_fase_metodologia" class="form-select" required>
            <option value="">Seleccione una fase</option>
            <?php foreach ($fases_metodologia as $fase): ?>
                <option value="<?= htmlspecialchars($fase['id_fase_metodologia']) ?>"
                    <?= (isset($formDataECS['id_fase_metodologia']) && $formDataECS['id_fase_metodologia'] == $fase['id_fase_metodologia']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($fase['nombre_fase']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($formErrorsECS['id_fase_metodologia'])): ?>
            <p class="error-message"><?= htmlspecialchars($formErrorsECS['id_fase_metodologia']) ?></p>
        <?php endif; ?>
    </div>
    
    <button type="submit" class="btn btn-primary mt-2">
        <i class="fas fa-plus mr-1"></i> Agregar ECS Personalizado
    </button>
</form>

<h3 class="text-xl font-semibold text-gray-700 mb-4 mt-8">ECS Activos en el Proyecto</h3>
<?php if (!empty($ecs_del_proyecto_detallados)): ?>
    <div class="overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                <tr>
                    <th class="px-4 py-2">ID Proy. ECS</th>
                    <th class="px-4 py-2">Nombre ECS (Catálogo)</th>
                    <th class="px-4 py-2">Tipo (Catálogo)</th>
                    <th class="px-4 py-2">Fase (Plantilla)</th>
                    <th class="px-4 py-2">Estado en Proyecto</th>
                    <th class="px-4 py-2">Comentario</th>
                    <th class="px-4 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php $ecsIndex = 0; foreach ($ecs_del_proyecto_detallados as $ecs_p): ?>
                    <tr class="border-b hover:bg-gray-100 <?= ($ecsIndex % 2 === 0) ? 'bg-white' : 'bg-gray-50'; ?>">
                        <td class="px-4 py-2"><?= htmlspecialchars($ecs_p['id_ecs_proyecto']) ?></td>
                        <td class="px-4 py-2 font-medium"><?= htmlspecialchars($ecs_p['nombre_ecs']) ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($ecs_p['tipo_ecs'] ?? 'N/A') ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($ecs_p['nombre_fase'] ?? 'Personalizado') ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($ecs_p['estado'] ?? 'N/A') ?></td>
                        <td class="px-4 py-2"><?= htmlspecialchars($ecs_p['comentario'] ?? '') ?></td>
                        <td class="px-4 py-2 space-x-1">
                            <a href="index.php?c=Proyecto&a=eliminarECSDelProyecto&id_ecs_proyecto=<?= $ecs_p['id_ecs_proyecto'] ?>&id_proyecto=<?= $id_proyecto_actual ?>&tab=ecs" class="btn btn-delete text-xs" onclick="return confirm('¿Está seguro de quitar este ECS del proyecto? Esto no elimina el ECS del catálogo general si es de plantilla.');">
                                <i class="fas fa-times-circle"></i> Quitar del Proyecto
                            </a>
                        </td>
                    </tr>
                <?php $ecsIndex++; endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="text-gray-500 italic">No hay Elementos de Configuración activos para este proyecto aún.</p>
<?php endif; ?>
