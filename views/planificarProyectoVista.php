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
        </div>

<div id="equipo" class="tab-content mt-6">
    <h2 class="section-title">Equipo del Proyecto</h2>

    <!-- Mostrar/Editar nombre del equipo -->
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
<!-- Lista de miembros asignados en tabla estilizada -->
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

                            class="bg-gray-600 bg-opacity-70 hover:bg-opacity-100 text-white px-3 py-1 rounded text-sm">
                            ✏️ Modificar Rol
                            </button>

                        </form>
                        <form method="post" action="index.php?c=Equipo&a=eliminarMiembro" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este miembro del equipo?');">
                        <input type="hidden" name="id_miembro" value="<?= isset($miembro['id_usuario']) ? htmlspecialchars($miembro['id_usuario']) : '' ?>">
                        <button type="submit" 
                        class="bg-gray-700 bg-opacity-70 hover:bg-opacity-100 text-white px-3 py-1 rounded text-sm">
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
        <div id="cronograma" class="tab-content mt-6">
            <h2 class="section-title">Cronograma del Proyecto</h2>
            <p class="text-gray-600">Funcionalidad de gestión de cronograma (fases, actividades, asignaciones, entregas) se implementará aquí.</p>
            </div>

        <div id="ecs" class="tab-content mt-6">
            <h2 class="section-title">Elementos de Configuración (ECS)</h2>
            <p class="text-gray-600">Funcionalidad de gestión de Elementos de Configuración del proyecto se implementará aquí.</p>
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
            event.currentTarget.classList.add("active");
        }
        
        document.addEventListener('DOMContentLoaded', () => {
            const firstTab = document.querySelector('.tab-button');
            if (firstTab) {
                firstTab.click();
            }
        });
        function abrirModal(id_usuario, id_rol) {
    const modal = document.getElementById('modalEditarRol');
    modal.classList.remove('hidden');  

    
    document.getElementById('modal_id_usuario').value = id_usuario;
    document.getElementById('modal_id_rol').value = id_rol;
}

function cerrarModal() {
    const modal = document.getElementById('modalEditarRol');
    modal.classList.add('hidden');  
}

    </script>

    <?php include __DIR__ . '/partials/footer.php';  ?>
</body>
</html>

<div id="modalEditarRol" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-lg w-full max-w-md">
        <h2 class="text-lg font-semibold mb-4">Editar Rol del Miembro</h2>
        <form id="formEditarRol" method="POST" action="index.php?c=Equipo&a=modificarRol">
            <input type="hidden" name="id_usuario" id="modal_id_usuario">
            <input type="hidden" name="id_equipo" value="<?= htmlspecialchars($equipo['id_equipo']) ?>">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Seleccionar nuevo rol:</label>
                <select name="id_rol_proyecto" id="modal_id_rol" class="mt-1 block w-full border border-gray-300 rounded-md p-2" required>
                    <option value="">-- Seleccionar rol --</option>
                    <?php foreach ($roles as $rol): ?>
                        <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre_rol']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="cerrarModal()" class="bg-gray-400 hover:bg-gray-500 text-white px-4 py-2 rounded">Cancelar</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Guardar</button>
            </div>
        </form>
    </div>
</div>

