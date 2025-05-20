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
        .tab-button { padding: 10px 20px; cursor: pointer; border-bottom: 2px solid transparent; }
        .tab-button.active { border-bottom-color: #4A90E2; color: #4A90E2; font-weight: bold; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
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
            <div class="p-3 mb-4 text-sm <?= $statusMessage['type'] === 'success' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' ?> rounded-lg" role="alert">
                <?= htmlspecialchars($statusMessage['text']) ?>
            </div>
        <?php endif; ?>

        <div class="mb-4 border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button onclick="openTab(event, 'general')" class="tab-button active">General</button>
                <button onclick="openTab(event, 'equipo')" class="tab-button">Equipo</button>
                <button onclick="openTab(event, 'cronograma')" class="tab-button">Cronograma</button>
                <button onclick="openTab(event, 'ecs')" class="tab-button">ECS</button>
            </nav>
        </div>

        <div id="general" class="tab-content active">
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
        </br>
        </div>

<div id="equipo" class="tab-content mt-6">
    <h2 class="section-title">Equipo del Proyecto</h2>
<?php if (!empty($equipo['nombre_equipo'])): ?>
    <div class="mb-4">
        <p class="text-sm font-medium text-gray-700">Nombre del Equipo:</p>
        <p class="mt-1 text-lg font-semibold text-blue-600"><?= htmlspecialchars($equipo['nombre_equipo']) ?></p>
    </div>
<?php else: ?>
    <form action="index.php?c=Equipo&a=guardarEquipo" method="POST" class="mb-6">
        <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nombre del Equipo:</label>
            <input type="text" name="nombre_equipo" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Guardar Nombre del Equipo
        </button>
    </form>
<?php endif; ?>

    <!-- Asignar miembros al equipo -->
    <h3 class="text-lg font-semibold text-gray-700 mb-2">Miembros del Equipo</h3>
    <?php if (isset($mensaje)): ?>
    <div class="<?= $mensaje['tipo'] === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700' ?> border px-4 py-3 rounded mb-4">
        <strong><?= ucfirst($mensaje['tipo']) ?>:</strong> <?= $mensaje['texto'] ?>
    </div>
<?php endif; ?>

    <form action="index.php?c=Equipo&a=asignarMiembro" method="POST" class="mb-6">
        <input type="hidden" name="id_equipo" value="<?= htmlspecialchars($equipo['id_equipo'] ?? '') ?>">
    <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Seleccionar Miembro:</label>
                <select name="id_usuario" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    <option value="">-- Seleccionar usuario --</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario['id_usuario'] ?>"><?= htmlspecialchars($usuario['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Rol en el Proyecto:</label>
                <select name="id_rol_proyecto" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    <option value="">-- Seleccionar rol --</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre_rol']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-4">
            <i class="fas fa-user-plus mr-1"></i> Asignar Miembro
        </button>
    </form>
<?php if (!empty($miembros_equipo)): ?>
    <h3 class="text-lg font-semibold text-gray-700 mb-4">Miembros Asignados</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow">
            <thead class="bg-gray-100 border-b border-gray-300">
                <tr>
                    <th class="text-left px-4 py-2 font-medium text-gray-700">Nombre Completo</th>
                    <th class="text-left px-4 py-2 font-medium text-gray-700">Rol</th>
                    <th class="text-left px-4 py-2 font-medium text-gray-700">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($miembros_equipo as $miembro): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-4 py-2"><?= htmlspecialchars($miembro['nombre_completo']) ?></td>
                        <td class="px-4 py-2 font-semibold text-blue-600"><?= htmlspecialchars($miembro['nombre_rol']) ?></td>
                        <td class="px-4 py-2 space-x-2">
                        <form method="post" action="index.php?c=Equipo&a=modificarRol" class="inline">
                            <input type="hidden" name="id_miembro" value="<?= isset($miembro['id_usuario']) ? htmlspecialchars($miembro['id_usuario']) : '' ?>">
                            <button type="button" 
                            onclick="abrirModal('<?= htmlspecialchars($miembro['id_usuario'] ?? '') ?>', '<?= isset($miembro['id_rol']) ? htmlspecialchars($miembro['id_rol']) : '' ?>')"
                             class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition-colors">
                            ✏️ Modificar Rol
                            </button>

                        </form>
                        <form method="post" action="index.php?c=Equipo&a=eliminarMiembro" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este miembro del equipo?');">
                        <input type="hidden" name="id_miembro" value="<?= isset($miembro['id_usuario']) ? htmlspecialchars($miembro['id_usuario']) : '' ?>">
                        <button type="submit" 
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition-colors">
                        🗑️ Eliminar
                        </button>
                        </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="text-gray-500 italic">No hay miembros asignados aún.</p>
<?php endif; ?>

</div>
        <div id="cronograma" class="tab-content mt-6">
        
        </div>
     <div id="ecs" class="tab-content mt-8 max-w-4xl mx-auto bg-white p-8 rounded-2xl shadow-lg border border-blue-100" data-id-proyecto="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">
    <?php if (!empty($fases)): ?>
        <h2 class="text-3xl md:text-4xl font-bold text-blue-700 mb-10 border-b pb-4 border-blue-200">
            Metodología: <span class="text-gray-800"><?= htmlspecialchars($proyecto['nombre_metodologia']) ?></span>
        </h2>

        <div class="space-y-8">
            <?php foreach ($fases as $fase): ?>
                <section class="bg-blue-50 p-6 rounded-xl border border-blue-200 shadow-sm">
                    <h3 class="text-2xl font-semibold text-blue-600 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m2 0a8 8 0 11-16 0 8 8 0 0116 0z" />
                        </svg>
                        <?= htmlspecialchars($fase['nombre_fase']) ?>
                    </h3>
                 

                    <?php if (!empty($fase['elementos'])): ?>
                        <ul class="space-y-3 ml-2">
                            <?php foreach ($fase['elementos'] as $elemento): ?>
                                <li>
                                    <label class="flex items-center gap-3 text-gray-700 hover:text-blue-600 transition-all">
                                        <input 
                                            type="checkbox" 
                                            name="elementos_seleccionados[]" 
                                            value="<?= htmlspecialchars($elemento['id']) ?>"
                                            class="accent-blue-500 w-5 h-5 transition"
                                        >
                                        <span class="text-base"><?= htmlspecialchars($elemento['nombre']) ?></span>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="italic text-gray-500 mt-2">No hay elementos de configuración para esta fase.</p>
                    <?php endif; ?>
                </section>
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <p class="text-center text-gray-500 italic">No hay fases para este proyecto.</p>
    <?php endif; ?>
        <div class="mt-10 text-right">
        <button 
            type="submit" 
            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-xl shadow-md transition"
        >
            Agregar elementos
        </button>
    </div>
</div>


<!-- Modal Editar Rol -->
<!-- Modal Editar Rol -->
<div id="modalEditarRol" class="fixed inset-0 z-50 hidden bg-black bg-opacity-60 flex items-center justify-center px-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full animate-fadeIn">
        <div class="flex justify-between items-center border-b border-gray-200 px-6 py-4">
            <h2 class="text-xl font-semibold text-gray-800">
                Editar Rol del Miembro: <span id="nombreMiembroModal" class="text-indigo-600"></span>
            </h2>
            <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600 transition text-3xl font-bold leading-none">&times;</button>
        </div>
        <form id="formEditarRol" method="POST" action="index.php?c=Equipo&a=modificarRol" class="px-6 py-6">
            <input type="hidden" name="id_miembro_equipo" id="modal_id_miembro_equipo_rol">
            <input type="hidden" name="id_proyecto_redirect" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">
            <div class="mb-5">
                <label for="modal_id_rol_proyecto" class="block mb-2 text-sm font-medium text-gray-700">Seleccionar nuevo rol:</label>
                <select name="id_rol_proyecto" id="modal_id_rol_proyecto" required
                    class="w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition">
                    <option value="">-- Seleccionar rol --</option>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $rol): ?>
                            <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre_rol']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="cerrarModal()" 
                    class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                    Cancelar
                </button>
                <button type="submit" 
                    class="px-4 py-2 rounded-md bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
<style>
@keyframes fadeIn {
  from {opacity: 0; transform: translateY(-10px);}
  to {opacity: 1; transform: translateY(0);}
}
.animate-fadeIn {
  animation: fadeIn 0.3s ease forwards;
}
</style>


<script>
    // Función para abrir el modal y rellenar datos
    function abrirModal(id_miembro_equipo, id_rol, nombre_miembro) {
        const modal = document.getElementById('modalEditarRol');
        modal.classList.remove('hidden');

        document.getElementById('modal_id_miembro_equipo_rol').value = id_miembro_equipo;
        document.getElementById('modal_id_rol_proyecto').value = id_rol;
        document.getElementById('nombreMiembroModal').textContent = nombre_miembro;
    }

    // Función para cerrar el modal
    function cerrarModal() {
        const modal = document.getElementById('modalEditarRol');
        modal.classList.add('hidden');
    }

    // Cerrar modal haciendo click fuera del contenido
    document.getElementById('modalEditarRol').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });

    // Funciones de pestañas (tab)
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
        event.currentTarget.classList.add("active");
    }

    document.addEventListener('DOMContentLoaded', () => {
        const firstTab = document.querySelector('.tab-button');
        if (firstTab) {
            firstTab.click();
        }
    });
</script>

<br>
</br>
</br>
    <?php include __DIR__ . '/partials/footer.php';  ?>
    
</div>
</body>
</html>



