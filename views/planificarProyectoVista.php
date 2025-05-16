<?php
// Asume que $baseUrl, $proyecto, $metodologias, $usuarios, $roles_proyecto, $equipo, $miembros_equipo,
// $actividades, $ecs_definidos, $formErrorsEquipo, $formDataEquipo, $formErrorsECS, $formDataECS,
// $statusMessage, $accion y $tituloPagina están disponibles desde el controlador.

// Incluir el header común
include __DIR__ . '/partials/header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Planificar Proyecto: <?= htmlspecialchars($proyecto['nombre_proyecto'] ?? 'Desconocido') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .section-title { font-size: 1.5em; font-weight: bold; color: #333; margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 2px solid #4A90E2; }
        .detail-item { margin-bottom: 0.75rem; }
        .detail-label { font-weight: bold; color: #555; }
        .btn { padding: 8px 12px; border-radius: 4px; text-decoration: none; display: inline-block; margin-right: 5px; font-size: 0.875rem; }
        .btn-primary { background-color: #4A90E2; color: white; border:none; }
        .btn-primary:hover { background-color: #357ABD; }
        .btn-secondary { background-color: #6c757d; color: white; border:none; }
        .btn-secondary:hover { background-color: #5a6268; }
        .btn-edit { background-color: #F5A623; color: white; }
        .btn-edit:hover { background-color: #D9931F; }
        .btn-delete { background-color: #D0021B; color: white; }
        .btn-delete:hover { background-color: #B00216; }
        .status-message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .status-message.success { background-color: #e6fffa; border: 1px solid #38a169; color: #2f855a; }
        .status-message.error { background-color: #fed7d7; border: 1px solid #e53e3e; color: #c53030; }
        .tab-button { padding: 10px 20px; cursor: pointer; border-bottom: 2px solid transparent; transition: all 0.3s ease; }
        .tab-button.active { border-bottom-color: #4A90E2; color: #4A90E2; font-weight: bold; }
        .tab-button:hover { border-bottom-color: #a0aec0; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .error-message { color: #D0021B; font-size: 0.875em; margin-top: 2px; margin-bottom: 10px; }
    </style>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/partials/header.php'; ?>

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-700">
                Planificación del Proyecto: <span class="text-blue-600"><?= htmlspecialchars($proyecto['nombre_proyecto'] ?? 'Desconocido') ?></span>
            </h1>
            <a href="index.php?c=Proyecto&a=index" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver a Proyectos
            </a>
        </div>

        <?php if (isset($statusMessage) && $statusMessage): ?>
            <div class="status-message <?= htmlspecialchars($statusMessage['type']) === 'success' ? 'success' : 'error' ?>">
                <?= htmlspecialchars($statusMessage['text']) ?>
            </div>
        <?php endif; ?>

        <div class="mb-4 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button onclick="openTab(event, 'general')" class="tab-button" data-tab-target="general">General</button>
                <button onclick="openTab(event, 'equipo')" class="tab-button" data-tab-target="equipo">Equipo</button>
                <button onclick="openTab(event, 'cronograma')" class="tab-button" data-tab-target="cronograma">Cronograma</button>
                <button onclick="openTab(event, 'ecs')" class="tab-button" data-tab-target="ecs">ECS</button>
            </nav>
        </div>

        <div id="general" class="tab-content">
            <h2 class="section-title">Información General del Proyecto</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="detail-item"><span class="detail-label">ID Proyecto:</span> <?= htmlspecialchars($proyecto['id_proyecto']) ?></p>
                    <p class="detail-item"><span class="detail-label">Nombre:</span> <?= htmlspecialchars($proyecto['nombre_proyecto']) ?></p>
                    <p class="detail-item"><span class="detail-label">Descripción:</span> <?= nl2br(htmlspecialchars($proyecto['descripcion'] ?? 'N/A')) ?></p>
                </div>
                <div>
                    <p class="detail-item"><span class="detail-label">Metodología:</span> <?= htmlspecialchars($proyecto['nombre_metodologia'] ?? 'No asignada') ?></p>
                    <p class="detail-item"><span class="detail-label">Product Owner:</span> <?= htmlspecialchars($proyecto['nombre_product_owner'] ?? 'No asignado') ?></p>
                    <p class="detail-item"><span class="detail-label">Fecha Inicio Planificada:</span> <?= htmlspecialchars($proyecto['fecha_inicio_planificada'] ? date('d/m/Y', strtotime($proyecto['fecha_inicio_planificada'])) : 'N/A') ?></p>
                    <p class="detail-item"><span class="detail-label">Fecha Fin Planificada:</span> <?= htmlspecialchars($proyecto['fecha_fin_planificada'] ? date('d/m/Y', strtotime($proyecto['fecha_fin_planificada'])) : 'N/A') ?></p>
                    <p class="detail-item"><span class="detail-label">Estado:</span> <?= htmlspecialchars($proyecto['estado_proyecto']) ?></p>
                </div>
            </div>
            <div class="mt-6">
                <a href="index.php?c=Proyecto&a=mostrarFormularioProyecto&id_proyecto=<?= $proyecto['id_proyecto'] ?>" class="btn btn-primary">
                    <i class="fas fa-edit mr-1"></i> Editar Datos Generales
                </a>
            </div>
        </div>

        <div id="equipo" class="tab-content mt-6">
            <h2 class="section-title">Equipo del Proyecto</h2>
            <?php if (isset($formErrorsEquipo['general_equipo'])): ?>
                <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    <?= htmlspecialchars($formErrorsEquipo['general_equipo']) ?>
                </div>
            <?php endif; ?>
            
            <form action="index.php?c=Equipo&a=guardarNombreEquipo" method="POST" class="mb-6">
                <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">
                <div class="mb-4">
                    <label for="nombre_equipo" class="block text-sm font-medium text-gray-700">Nombre del Equipo:</label>
                    <input type="text" name="nombre_equipo" id="nombre_equipo" class="mt-1 block w-full md:w-1/2 border border-gray-300 rounded-md p-2" 
                           value="<?= htmlspecialchars($equipo['nombre_equipo'] ?? $formDataEquipo['nombre_equipo'] ?? '') ?>" required>
                     <?php if (isset($formErrorsEquipo['nombre_equipo'])): ?>
                        <p class="error-message"><?= htmlspecialchars($formErrorsEquipo['nombre_equipo']) ?></p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> <?= isset($equipo['id_equipo']) ? 'Actualizar Nombre Equipo' : 'Guardar Nombre Equipo' ?>
                </button>
            </form>
            
            <?php if (isset($equipo['id_equipo'])): // Solo mostrar asignación si el equipo ya tiene ID ?>
            <h3 class="text-lg font-semibold text-gray-700 mt-6 mb-2">Asignar Miembros al Equipo "<?= htmlspecialchars($equipo['nombre_equipo']) ?>"</h3>
            <form action="index.php?c=Equipo&a=asignarMiembro" method="POST" class="mb-6 p-4 border rounded-md bg-gray-50">
                <input type="hidden" name="id_equipo" value="<?= htmlspecialchars($equipo['id_equipo']) ?>">
                <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>"> <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="id_usuario_asignar" class="block text-sm font-medium text-gray-700">Seleccionar Miembro:</label>
                        <select name="id_usuario" id="id_usuario_asignar" class="form-select mt-1 block w-full" required>
                            <option value="">-- Seleccionar usuario --</option>
                            <?php if(!empty($usuarios)): ?>
                                <?php foreach ($usuarios as $usuario_item): ?>
                                    <option value="<?= $usuario_item['id_usuario'] ?>"><?= htmlspecialchars($usuario_item['nombre_completo']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (isset($formErrorsEquipo['id_usuario'])): ?><p class="error-message"><?= $formErrorsEquipo['id_usuario'] ?></p><?php endif; ?>
                    </div>
                    <div>
                        <label for="id_rol_proyecto_asignar" class="block text-sm font-medium text-gray-700">Rol en el Proyecto:</label>
                        <select name="id_rol_proyecto" id="id_rol_proyecto_asignar" class="form-select mt-1 block w-full" required>
                            <option value="">-- Seleccionar rol --</option>
                            <?php if(!empty($roles_proyecto)): ?>
                                <?php foreach ($roles_proyecto as $rol): ?>
                                    <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre_rol']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                         <?php if (isset($formErrorsEquipo['id_rol_proyecto'])): ?><p class="error-message"><?= $formErrorsEquipo['id_rol_proyecto'] ?></p><?php endif; ?>
                    </div>
                    <div class="self-end">
                        <button type="submit" class="btn btn-primary w-full md:w-auto">
                            <i class="fas fa-user-plus mr-1"></i> Asignar Miembro
                        </button>
                    </div>
                </div>
                 <?php if (isset($formErrorsEquipo['asignacion'])): ?><p class="error-message mt-2"><?= $formErrorsEquipo['asignacion'] ?></p><?php endif; ?>
            </form>

            <h3 class="text-lg font-semibold text-gray-700 mb-2 mt-6">Miembros Asignados</h3>
            <?php if (!empty($miembros_equipo)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
                        <thead class="bg-gray-100 border-b border-gray-300">
                            <tr>
                                <th class="text-left px-4 py-2 font-medium text-gray-700">Nombre Completo</th>
                                <th class="text-left px-4 py-2 font-medium text-gray-700">Rol en Proyecto</th>
                                <th class="text-left px-4 py-2 font-medium text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($miembros_equipo as $miembro): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= htmlspecialchars($miembro['nombre_completo']) ?></td>
                                    <td class="px-4 py-2 font-semibold text-blue-600"><?= htmlspecialchars($miembro['nombre_rol']) ?></td>
                                    <td class="px-4 py-2 space-x-1">
                                        <button type="button" 
                                            onclick="abrirModalEditarRol('<?= htmlspecialchars($miembro['id_miembro_equipo'] ?? '') ?>', '<?= htmlspecialchars($miembro['id_rol_proyecto'] ?? '') ?>', '<?= htmlspecialchars($miembro['nombre_completo'], ENT_QUOTES) ?>')"
                                            class="btn btn-edit text-xs">
                                            <i class="fas fa-user-edit"></i> Modificar Rol
                                        </button>
                                        <form method="POST" action="index.php?c=Equipo&a=eliminarMiembroEquipo" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar a <?= htmlspecialchars($miembro['nombre_completo']) ?> del equipo?');">
                                            <input type="hidden" name="id_miembro_equipo" value="<?= htmlspecialchars($miembro['id_miembro_equipo'] ?? '') ?>">
                                            <input type="hidden" name="id_proyecto_redirect" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">
                                            <button type="submit" class="btn btn-delete text-xs">
                                                <i class="fas fa-user-minus"></i> Quitar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 italic">No hay miembros asignados a este equipo aún.</p>
            <?php endif; ?>
            <?php else: ?>
                 <p class="text-gray-500 italic">Guarde primero el nombre del equipo para poder asignar miembros.</p>
            <?php endif; ?>
        </div>

        <div id="cronograma" class="tab-content mt-6">
            <h2 class="section-title">Cronograma del Proyecto</h2>
            <p class="text-gray-600">Funcionalidad de gestión de cronograma (fases, actividades, asignaciones, entregas) se implementará aquí.</p>
            </div>

        <div id="ecs" class="tab-content mt-6">
            <h2 class="section-title">Elementos de Configuración (ECS)</h2>
            <?php if (isset($formErrorsECS['general_ecs'])): ?>
                <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                    <?= htmlspecialchars($formErrorsECS['general_ecs']) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?c=Proyecto&a=agregarECSProyecto" method="POST" class="mb-6 p-4 border rounded-md bg-gray-50">
                <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Definir Nuevo ECS</h3>
                
                <div class="mb-4">
                    <label for="nombre_ecs" class="form-label">Nombre del ECS:</label>
                    <input type="text" name="nombre_ecs" id="nombre_ecs" class="form-input" 
                           value="<?= htmlspecialchars($formDataECS['nombre_ecs'] ?? '') ?>" required>
                    <?php if (isset($formErrorsECS['nombre_ecs'])): ?>
                        <p class="error-message"><?= htmlspecialchars($formErrorsECS['nombre_ecs']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label for="tipo_ecs" class="form-label">Tipo de ECS:</label>
                    <input type="text" name="tipo_ecs" id="tipo_ecs" class="form-input" 
                           placeholder="Ej: Documento, Módulo de Código, Plan"
                           value="<?= htmlspecialchars($formDataECS['tipo_ecs'] ?? '') ?>">
                     <?php if (isset($formErrorsECS['tipo_ecs'])): ?>
                        <p class="error-message"><?= htmlspecialchars($formErrorsECS['tipo_ecs']) ?></p>
                    <?php endif; ?>
                </div>
            
                <div class="mb-4">
                    <label for="descripcion_ecs" class="form-label">Descripción del ECS (Opcional):</label>
                    <textarea name="descripcion_ecs" id="descripcion_ecs" rows="3" 
                              class="form-textarea"><?= htmlspecialchars($formDataECS['descripcion_ecs'] ?? '') ?></textarea>
                </div>

                <div class="mt-4">
                    <label for="id_actividad_asociada" class="form-label">Asociar a Actividad (Opcional):</label>
                    <select name="id_actividad_asociada" id="id_actividad_asociada" class="form-select">
                        <option value="">-- Ninguna --</option>
                        <?php if (!empty($actividades)): ?>
                            <?php foreach ($actividades as $actividad): ?>
                                <option value="<?= htmlspecialchars($actividad['id_actividad']) ?>"
                                    <?= (isset($formDataECS['id_actividad_asociada']) && $formDataECS['id_actividad_asociada'] == $actividad['id_actividad']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($actividad['nombre_actividad']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay actividades definidas para este proyecto.</option>
                        <?php endif; ?>
                    </select>
                </div>
                

                <button type="submit" class="btn btn-primary mt-4">
                    <i class="fas fa-plus mr-1"></i> Agregar ECS
                </button>
            </form>

            <h3 class="text-lg font-semibold text-gray-700 mb-2 mt-6">ECS Definidos para el Proyecto</h3>
            <?php if (!empty($ecs_definidos)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
                        <thead class="bg-gray-100 border-b border-gray-300">
                            <tr>
                                <th class="text-left px-4 py-2 font-medium text-gray-700">ID</th>
                                <th class="text-left px-4 py-2 font-medium text-gray-700">Nombre ECS</th>
                                <th class="text-left px-4 py-2 font-medium text-gray-700">Tipo</th>
                                <th class="text-left px-4 py-2 font-medium text-gray-700">Versión</th>
                                <th class="text-left px-4 py-2 font-medium text-gray-700">Estado</th>
                                <th class="text-left px-4 py-2 font-medium text-gray-700">Creador</th>
                                <th class="text-left px-4 py-2 font-medium text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ecs_definidos as $ecs): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-4 py-2"><?= htmlspecialchars($ecs['id_ecs']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($ecs['nombre_ecs']) ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($ecs['tipo_ecs'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($ecs['version_actual'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($ecs['estado_ecs'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-2"><?= htmlspecialchars($ecs['nombre_creador'] ?? 'N/A') ?></td>
                                    <td class="px-4 py-2 space-x-1">
                                        <a href="index.php?c=Proyecto&a=eliminarECSProyecto&id_ecs=<?= $ecs['id_ecs'] ?>&id_proyecto=<?= $proyecto['id_proyecto'] ?>&tab=ecs" class="btn btn-delete text-xs" onclick="return confirm('¿Está seguro de eliminar este Elemento de Configuración?');">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 italic">No hay Elementos de Configuración definidos para este proyecto aún.</p>
            <?php endif; ?>
        </div>

    </div> <div id="modalEditarRol" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Editar Rol del Miembro: <span id="nombreMiembroModal"></span></h2>
                <button onclick="cerrarModalEditarRol()" class="text-gray-500 hover:text-gray-700">&times;</button>
            </div>
            <form id="formEditarRol" method="POST" action="index.php?c=Equipo&a=modificarRolMiembro">
                <input type="hidden" name="id_miembro_equipo" id="modal_id_miembro_equipo_rol">
                <input type="hidden" name="id_proyecto_redirect" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">
                <div class="mb-4">
                    <label for="modal_id_rol_proyecto" class="block text-sm font-medium text-gray-700">Seleccionar nuevo rol:</label>
                    <select name="id_rol_proyecto" id="modal_id_rol_proyecto" class="form-input mt-1 block w-full" required>
                        <option value="">-- Seleccionar rol --</option>
                        <?php if (!empty($roles_proyecto)): ?>
                            <?php foreach ($roles_proyecto as $rol): ?>
                                <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre_rol']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="cerrarModalEditarRol()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

<script>
    function openTab(event, tabName) {
        let i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
            tabcontent[i].classList.remove("active");
        }
        tablinks = document.getElementsByClassName("tab-button");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active");
        if (event && event.currentTarget) { // Check if event and currentTarget exist
            event.currentTarget.classList.add("active");
        }
        // Guardar la pestaña activa en localStorage
        localStorage.setItem('activeProjectPlanTab_<?= $proyecto['id_proyecto'] ?>', tabName);
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Obtener la pestaña activa desde la URL o localStorage
        const urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('tab');
        
        if (!activeTab) { // Si no hay tab en URL, intentar desde localStorage
            activeTab = localStorage.getItem('activeProjectPlanTab_<?= $proyecto['id_proyecto'] ?>') || 'general';
        }

        const tabButtonToActivate = document.querySelector(`.tab-button[data-tab-target='${activeTab}']`);
        
        if (tabButtonToActivate) {
            // Simulamos un evento click para que la lógica de openTab se ejecute correctamente
            // incluyendo el event.currentTarget
            const clickEvent = new MouseEvent('click', {
                bubbles: true,
                cancelable: true,
                view: window
            });
            tabButtonToActivate.dispatchEvent(clickEvent);
            // openTab({currentTarget: tabButtonToActivate}, activeTab); // Llamada anterior
        } else {
            const firstTabButton = document.querySelector('.tab-button');
            if (firstTabButton) {
                const clickEvent = new MouseEvent('click', {
                    bubbles: true,
                    cancelable: true,
                    view: window
                });
                firstTabButton.dispatchEvent(clickEvent);
                // openTab({currentTarget: firstTabButton}, firstTabButton.dataset.tabTarget); // Llamada anterior
            }
        }
    });

    function abrirModalEditarRol(id_miembro_equipo, id_rol_actual, nombre_miembro) {
        const modal = document.getElementById('modalEditarRol');
        if (modal) {
            modal.classList.remove('hidden');
            document.getElementById('nombreMiembroModal').textContent = nombre_miembro;
            document.getElementById('modal_id_miembro_equipo_rol').value = id_miembro_equipo;
            document.getElementById('modal_id_rol_proyecto').value = id_rol_actual;
        } else {
            console.error("Modal con ID 'modalEditarRol' no encontrado.");
        }
    }

    function cerrarModalEditarRol() {
        const modal = document.getElementById('modalEditarRol');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
