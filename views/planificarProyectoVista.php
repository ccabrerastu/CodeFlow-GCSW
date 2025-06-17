<?php
include __DIR__ . '/partials/header.php';

$id_proyecto_actual = $proyecto['id_proyecto'] ?? null;
$id_cronograma_actual = $cronograma['id_cronograma'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Planificar Proyecto: <?= htmlspecialchars($proyecto['nombre_proyecto'] ?? 'Desconocido') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        .tab-button {
    padding: 0.5rem 1rem;
    border-bottom: 2px solid transparent;
    transition: all 0.3s ease;
}
.tab-button:hover {
    color: #1e40af;
    border-color: #d1d5db;
}
.tab-button.active {
    color: #1d4ed8;
    border-color: #1d4ed8;
    font-weight: 600;
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}
.status-message {
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border-radius: 0.375rem;
    font-weight: 500;
}
.status-message.success {
    background-color: #ecfdf5;
    border: 1px solid #10b981;
    color: #065f46;
}
.status-message.error {
    background-color: #fef2f2;
    border: 1px solid #ef4444;
    color: #991b1b;
}
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
        .tab-content { display: none; padding-top: 1rem; }
        .tab-content.active { display: block; animation: fadeIn 0.5s; }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .error-message { color: #D0021B; font-size: 0.875em; margin-top: 2px; margin-bottom: 10px; }
        @keyframes fadeIn {
          from {opacity: 0; transform: translateY(-10px);}
          to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>
<body class="bg-gray-100">

      <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <!-- Título y botón de regreso -->
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
    <h1 class="text-2xl md:text-3xl font-semibold text-gray-800 tracking-wide font-sans leading-relaxed">
    <span class="block md:inline text-gray-500 ">Planificación del Proyecto:</span>
    <span class="block md:inline text-blue-500 font-medium not-italic">
        <?= htmlspecialchars($proyecto['nombre_proyecto'] ?? 'Desconocido') ?>
    </span>
</h1>

    <a href="index.php?c=Proyecto&a=index"
       class="inline-flex items-center gap-2 px-4 py-2 rounded-md border border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-100 hover:text-blue-600 shadow-sm transition">
        <i class="fas fa-arrow-left"></i>
        Volver a Proyectos
    </a>
</div>
        <?php if (isset($statusMessage) && $statusMessage): ?>
            <div class="status-message <?= htmlspecialchars($statusMessage['type']) === 'success' ? 'success' : 'error' ?>">
                <?= htmlspecialchars($statusMessage['text']) ?>
            </div>
        <?php endif; ?>

        <div class="mb-4 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 text-sm font-medium text-gray-500" aria-label="Tabs">
                <button onclick="openTab(event, 'general')" class="tab-button" data-tab-target="general">General</button>
                <button onclick="openTab(event, 'equipo')" class="tab-button" data-tab-target="equipo">Equipo</button>
                <button onclick="openTab(event, 'cronograma')" class="tab-button" data-tab-target="cronograma">Cronograma</button>
                <button onclick="openTab(event, 'ecs')" class="tab-button" data-tab-target="ecs">ECS</button>
            </nav>
        </div>

        <div id="general" class="tab-content">
            <?php include __DIR__ . '/proyecto/visualizarInformacionVista.php'; ?>
        </div>

        <div id="equipo" class="tab-content mt-6">
            <?php include __DIR__ . '/proyecto/gestionarEquipoVista.php'; ?>
        </div>

        <div id="cronograma" class="tab-content mt-6">
            <?php include __DIR__ . '/proyecto/gestionarCronogramaVista.php'; ?>
        </div>

        <div id="ecs" class="tab-content mt-6">
            <?php include __DIR__ . '/proyecto/gestionarEcsVista.php'; ?>
        </div>

    </div> <div id="modalEditarRol" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Editar Rol del Miembro: <span id="nombreMiembroModal"></span></h2>
                <button onclick="cerrarModalEditarRol()" class="text-gray-600 hover:text-gray-800 text-2xl">&times;</button>
            </div>
            <form id="formEditarRol" method="POST" action="index.php?c=Equipo&a=modificarRolMiembro">
                <input type="hidden" name="id_miembro_equipo" id="modal_id_miembro_equipo_rol">
                <input type="hidden" name="id_proyecto_redirect" value="<?= htmlspecialchars($id_proyecto_actual) ?>">
                <div class="mb-4">
                    <label for="modal_id_rol_proyecto" class="form-label">Seleccionar nuevo rol:</label>
                    <select name="id_rol_proyecto" id="modal_id_rol_proyecto" class="form-input mt-1 block w-full" required>
                        <option value="">-- Seleccionar rol --</option>
                        <?php if (!empty($roles_proyecto)): ?>
                            <?php foreach ($roles_proyecto as $rol_item): ?>
                                <option value="<?= $rol_item['id_rol'] ?>"><?= htmlspecialchars($rol_item['nombre_rol']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="cerrarModalEditarRol()" class="btn btn-secondary">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalEditarActividad" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-lg shadow-xl w-full max-w-lg">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold">Editar Actividad: <span id="nombreActividadModal"></span></h2>
                <button onclick="cerrarModalEditarActividad()" class="text-gray-600 hover:text-gray-800 text-2xl">&times;</button>
            </div>
            <form id="formEditarActividad" method="POST" action="index.php?c=Proyecto&a=actualizarActividadCronograma">
                <input type="hidden" name="id_actividad" id="modal_id_actividad">
                <input type="hidden" name="id_proyecto_redirect" value="<?= htmlspecialchars($id_proyecto_actual) ?>">
                <input type="hidden" name="id_cronograma" id="modal_id_cronograma" value="<?= htmlspecialchars($id_cronograma_actual) ?>">

                <div class="mb-4">
                    <label for="modal_nombre_actividad" class="form-label">Nombre de la Actividad:</label>
                    <input type="text" name="nombre_actividad" id="modal_nombre_actividad" class="form-input" required>
                </div>
                <div class="mb-4">
                    <label for="modal_descripcion_actividad" class="form-label">Descripción:</label>
                    <textarea name="descripcion_actividad" id="modal_descripcion_actividad" rows="2" class="form-textarea"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="modal_id_fase_metodologia" class="form-label">Fase:</label>
                        <select name="id_fase_metodologia" id="modal_id_fase_metodologia" class="form-select">
                            <option value="">-- Ninguna --</option>
                            <?php if (!empty($fases_metodologia)): ?>
                                <?php foreach ($fases_metodologia as $fase_met): ?>
                                    <option value="<?= htmlspecialchars($fase_met['id_fase_metodologia']) ?>"><?= htmlspecialchars($fase_met['nombre_fase']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div>
                        <label for="modal_id_responsable" class="form-label">Responsable:</label>
                        <select name="id_responsable" id="modal_id_responsable" class="form-select">
                            <option value="">-- Ninguno --</option>
                            <?php if (!empty($miembros_equipo)): ?>
                                <?php foreach ($miembros_equipo as $miembro): ?>
                                    <option value="<?= htmlspecialchars($miembro['id_usuario']) ?>"><?= htmlspecialchars($miembro['nombre_completo']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="modal_fecha_inicio_planificada" class="form-label">Fecha Inicio Plan.:</label>
                        <input type="date" name="fecha_inicio_planificada" id="modal_fecha_inicio_planificada" class="form-input">
                    </div>
                    <div>
                        <label for="modal_fecha_fin_planificada" class="form-label">Fecha Fin Plan.:</label>
                        <input type="date" name="fecha_fin_planificada" id="modal_fecha_fin_planificada" class="form-input">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="modal_fecha_entrega_real" class="form-label">Fecha Entrega Real:</label>
                        <input type="date" name="fecha_entrega_real" id="modal_fecha_entrega_real" class="form-input">
                    </div>
                    <div>
                        <label for="modal_estado_actividad" class="form-label">Estado:</label>
                        <select name="estado_actividad" id="modal_estado_actividad" class="form-select" required>
                            <option value="Pendiente">Pendiente</option>
                            <option value="En Progreso">En Progreso</option>
                            <option value="Completada">Completada</option>
                            <option value="Atrasada">Atrasada</option>
                            <option value="Bloqueada">Bloqueada</option>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="modal_id_ecs_entregable" class="form-label">ECS Entregable Principal:</label>
                    <select name="id_ecs_entregable" id="modal_id_ecs_entregable" class="form-select">
                        <option value="">-- Ninguno --</option>
                        <?php if (!empty($ecs_del_proyecto_detallados)): ?>
                            <?php foreach ($ecs_del_proyecto_detallados as $ecs_item): ?>
                                <option value="<?= htmlspecialchars($ecs_item['id_ecs']) ?>">
                                    <?= htmlspecialchars($ecs_item['nombre_ecs']) ?> (ID: <?= htmlspecialchars($ecs_item['id_ecs']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                             <option value="" disabled>No hay ECS definidos/seleccionados para este proyecto.</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="cerrarModalEditarActividad()" class="btn btn-secondary">Cancelar</button>
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
            if (tablinks[i] === event.currentTarget) {
                tablinks[i].classList.add("active");
            }
        }
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active");
        
        if (typeof projectId !== 'undefined' && projectId) {
            localStorage.setItem('activeProjectPlanTab_' + projectId, tabName);
        } else {
            localStorage.setItem('activeProjectPlanTab_default', tabName);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const projectIdForTab = '<?= $id_proyecto_actual ?? 'default' ?>';
        const urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('tab');
        
        if (!activeTab) { 
            activeTab = localStorage.getItem('activeProjectPlanTab_' + projectIdForTab) || 'general';
        }

        const tabButtonToActivate = document.querySelector(`.tab-button[data-tab-target='${activeTab}']`);
        
        if (tabButtonToActivate) {
            const clickEvent = new MouseEvent('click', { bubbles: true, cancelable: true, view: window });
            tabButtonToActivate.dispatchEvent(clickEvent);
        } else {
            const firstTabButton = document.querySelector('.tab-button[data-tab-target="general"]');
            if (firstTabButton) {
               const clickEvent = new MouseEvent('click', { bubbles: true, cancelable: true, view: window });
                firstTabButton.dispatchEvent(clickEvent);
            }
        }
    });

    function abrirModalEditarRol(id_miembro_equipo, id_rol_actual, nombre_miembro) {
        const modal = document.getElementById('modalEditarRol');
        if (modal) {
            modal.classList.remove('hidden');
            const nombreMiembroModalEl = document.getElementById('nombreMiembroModal');
            const modalIdMiembroEquipoRolEl = document.getElementById('modal_id_miembro_equipo_rol');
            const modalIdRolProyectoEl = document.getElementById('modal_id_rol_proyecto');

            if (nombreMiembroModalEl) nombreMiembroModalEl.textContent = nombre_miembro;
            if (modalIdMiembroEquipoRolEl) modalIdMiembroEquipoRolEl.value = id_miembro_equipo;
            if (modalIdRolProyectoEl) modalIdRolProyectoEl.value = id_rol_actual;
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
    
    const modalEditarRolElement = document.getElementById('modalEditarRol');
    if (modalEditarRolElement) {
        modalEditarRolElement.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalEditarRol();
            }
        });
    }

    function abrirModalEditarActividad(actividad) {
        const modal = document.getElementById('modalEditarActividad');
        if (modal) {
            modal.classList.remove('hidden');
            document.getElementById('nombreActividadModal').textContent = actividad.nombre_actividad;
            document.getElementById('modal_id_actividad').value = actividad.id_actividad;
            document.getElementById('modal_nombre_actividad').value = actividad.nombre_actividad;
            document.getElementById('modal_descripcion_actividad').value = actividad.descripcion || '';
            document.getElementById('modal_id_fase_metodologia').value = actividad.id_fase_metodologia || '';
            document.getElementById('modal_id_responsable').value = actividad.id_responsable || '';
            document.getElementById('modal_fecha_inicio_planificada').value = actividad.fecha_inicio_planificada || '';
            document.getElementById('modal_fecha_fin_planificada').value = actividad.fecha_fin_planificada || '';
            document.getElementById('modal_fecha_entrega_real').value = actividad.fecha_entrega_real || '';
            document.getElementById('modal_estado_actividad').value = actividad.estado_actividad || 'Pendiente';
            document.getElementById('modal_id_cronograma').value = actividad.id_cronograma || '<?= htmlspecialchars($id_cronograma_actual ?? '') ?>';
            
            document.getElementById('modal_id_ecs_entregable').value = actividad.id_ecs_entregable || ''; 
        } else {
            console.error("Modal con ID 'modalEditarActividad' no encontrado.");
        }
    }

    function cerrarModalEditarActividad() {
        const modal = document.getElementById('modalEditarActividad');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
    const modalEditarActividadElement = document.getElementById('modalEditarActividad');
    if(modalEditarActividadElement) {
        modalEditarActividadElement.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModalEditarActividad();
            }
        });
    }

    const showBtn = document.getElementById('show-custom-ecs-form-btn');
    const hideBtn = document.getElementById('hide-custom-ecs-form-btn');
    const formContainer = document.getElementById('custom-ecs-form-container');

    if (showBtn && hideBtn && formContainer) {
        showBtn.addEventListener('click', () => {
            formContainer.classList.remove('hidden');
            showBtn.classList.add('hidden');
        });

        hideBtn.addEventListener('click', () => {
            formContainer.classList.add('hidden');
            showBtn.classList.remove('hidden');
        });
    }

    <?php if (!empty($formErrorsECS)): ?>
        document.addEventListener('DOMContentLoaded', () => {
            if (showBtn) {
                showBtn.click();
            }
        });
    <?php endif; ?>


</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>