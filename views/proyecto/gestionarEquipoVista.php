<div class="bg-white shadow-lg rounded-2xl p-6 mb-10 border border-gray-200">
    <h2 class="text-2xl font-bold text-gray-800 mb-5">Equipo del Proyecto</h2>

    <?php if (empty($equipo['nombre_equipo'])): ?>
        <form action="index.php?c=Equipo&a=guardarEquipo" method="POST" id="formSeleccionEquipo" class="space-y-5">
            <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar Equipo Existente o Crear Nuevo:</label>
                <select name="id_equipo" id="selectEquipo" onchange="mostrarFormularioNuevoEquipo(this.value)"
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">-- Seleccionar equipo --</option>
                    <?php foreach ($equipos_existentes as $eq): ?>
                        <option value="<?= $eq['id_equipo'] ?>"><?= htmlspecialchars($eq['nombre_equipo']) ?></option>
                    <?php endforeach; ?>
                    <option value="nuevo">➕ Crear nuevo equipo</option>
                </select>
            </div>

            <div id="formNuevoEquipo" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Nuevo Equipo:</label>
                <input type="text" name="nombre_equipo"
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <button type="submit"
                class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-save mr-2"></i> Guardar Equipo
            </button>
        </form>
    <?php else: ?>
        <div class="mb-4">
            <p class="text-sm text-gray-600">Nombre del Equipo:</p>
            <p class="text-xl font-semibold text-blue-700"><?= htmlspecialchars($equipo['nombre_equipo']) ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Asignar miembros -->
<div class="bg-white shadow-lg rounded-2xl p-6 mb-10 border border-gray-200">
    <h3 class="text-xl font-bold text-gray-800 mb-5">Miembros del Equipo</h3>

    <?php if (isset($mensaje)): ?>
        <div class="<?= $mensaje['tipo'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?> border border-gray-300 px-4 py-3 rounded-lg mb-5">
            <strong><?= ucfirst($mensaje['tipo']) ?>:</strong> <?= $mensaje['texto'] ?>
        </div>
    <?php endif; ?>

    <form action="index.php?c=Equipo&a=asignarMiembro" method="POST" class="space-y-5">
        <input type="hidden" name="id_equipo" value="<?= htmlspecialchars($equipo['id_equipo'] ?? '') ?>">
        <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Seleccionar Miembro:</label>
                <select name="id_usuario"
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">-- Seleccionar usuario --</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario['id_usuario'] ?>"><?= htmlspecialchars($usuario['nombre_completo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rol en el Proyecto:</label>
                <select name="id_rol_proyecto"
                    class="w-full border-gray-300 rounded-lg p-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">-- Seleccionar rol --</option>
                    <?php foreach ($roles_proyecto as $rol): ?>
                        <option value="<?= $rol['id_rol'] ?>"><?= htmlspecialchars($rol['nombre_rol']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit"
            class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-user-plus mr-2"></i> Asignar Miembro
        </button>
    </form>
</div>

<!-- Tabla de miembros -->
<?php if (!empty($miembros_equipo)): ?>
    <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200">
        <h3 class="text-xl font-bold text-gray-800 mb-5">Miembros Asignados</h3>
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-100 text-gray-700 text-sm">
                    <tr>
                        <th class="px-4 py-2 text-left">Nombre</th>
                        <th class="px-4 py-2 text-left">Rol</th>
                        <th class="px-4 py-2 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($miembros_equipo as $miembro): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2"><?= htmlspecialchars($miembro['nombre_completo']) ?></td>
                            <td class="px-4 py-2 text-indigo-600 font-medium"><?= htmlspecialchars($miembro['nombre_rol']) ?></td>
                            <td class="px-4 py-2 space-x-2">
                                <button type="button"
                                    onclick="abrirModalEditarRol(
                                        '<?= htmlspecialchars($miembro['id_usuario'] ?? '') ?>',
                                        '<?= htmlspecialchars($miembro['id_rol'] ?? '') ?>'
                                    )"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm transition">
                                    ✏️ Modificar
                                </button>

                                <form method="post" action="index.php?c=Equipo&a=eliminarMiembro" class="inline"
                                    onsubmit="return confirm('¿Estás seguro de eliminar este miembro del equipo?');">
                                    <input type="hidden" name="id_miembro" value="<?= htmlspecialchars($miembro['id_usuario']) ?>">
                                    <input type="hidden" name="id_equipo" value="<?= htmlspecialchars($equipo['id_equipo']) ?>">
                                    <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($proyecto['id_proyecto']) ?>">
                                    <button type="submit"
                                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm transition">
                                        🗑️ Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php else: ?>
    <p class="text-gray-500 italic">No hay miembros asignados aún.</p>
<?php endif; ?>
<!-- Modal sigue igual, con estilos ya apropiados -->


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
                    <?php if (!empty($roles_proyecto)): ?>
                        <?php foreach ($roles_proyecto as $rol): ?>
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
<script>
function mostrarFormularioNuevoEquipo(valor) {
    const formNuevo = document.getElementById('formNuevoEquipo');
    const inputNombre = formNuevo.querySelector('input[name="nombre_equipo"]');

    if (valor === "nuevo") {
        formNuevo.classList.remove('hidden');
        inputNombre.setAttribute('required', 'required');
    } else {
        formNuevo.classList.add('hidden');
        inputNombre.removeAttribute('required');
    }
}

</script>