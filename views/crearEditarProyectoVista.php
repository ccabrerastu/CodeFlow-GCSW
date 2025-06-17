<?php $esEditar = isset($accion) && $accion === 'editar'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - <?= htmlspecialchars($tituloPagina) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
</head>
<body class="bg-gradient-to-br from-blue-50 to-white min-h-screen">

<?php include __DIR__ . '/partials/header.php'; ?>

<div class="max-w-3xl mx-auto mt-12 p-8 bg-white rounded-2xl shadow-2xl">
    <h1 class="text-3xl font-bold text-blue-800 mb-8 border-b pb-2"><?= htmlspecialchars($tituloPagina) ?></h1>

    <?php if (isset($formErrors['general'])): ?>
        <div class="mb-6 p-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded-lg shadow">
            <i class="fas fa-exclamation-triangle mr-2"></i><?= htmlspecialchars($formErrors['general']) ?>
        </div>
    <?php endif; ?>

    <form action="<?= $baseUrl ?>index.php?c=Proyecto&a=guardarProyecto" method="POST" class="space-y-6">

        <?php if ($esEditar && isset($formData['id_proyecto'])): ?>
            <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($formData['id_proyecto']) ?>">
        <?php endif; ?>

        <!-- Nombre del Proyecto -->
        <div>
            <label for="nombre_proyecto" class="block font-semibold text-gray-700 mb-1">Nombre del Proyecto <span class="text-red-500">*</span></label>
            <input type="text" id="nombre_proyecto" name="nombre_proyecto" required
                   class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                   value="<?= htmlspecialchars($formData['nombre_proyecto'] ?? '') ?>">
            <?php if (isset($formErrors['nombre_proyecto'])): ?>
                <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrors['nombre_proyecto']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Descripción -->
        <div>
            <label for="descripcion" class="block font-semibold text-gray-700 mb-1">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm resize-none focus:ring-2 focus:ring-blue-500 focus:outline-none"><?= htmlspecialchars($formData['descripcion'] ?? '') ?></textarea>
            <?php if (isset($formErrors['descripcion'])): ?>
                <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrors['descripcion']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Selección de Metodología y Product Owner -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="id_metodologia" class="block font-semibold text-gray-700 mb-1">Metodología <span class="text-red-500">*</span></label>
                <select id="id_metodologia" name="id_metodologia" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Seleccione una opción</option>
                    <?php foreach ($metodologias as $metodologia): ?>
                        <option value="<?= htmlspecialchars($metodologia['id_metodologia']) ?>"
                            <?= (isset($formData['id_metodologia']) && $formData['id_metodologia'] == $metodologia['id_metodologia']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($metodologia['nombre_metodologia']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($formErrors['id_metodologia'])): ?>
                    <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrors['id_metodologia']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="id_product_owner" class="block font-semibold text-gray-700 mb-1">Product Owner / Jefe</label>
                <select id="id_product_owner" name="id_product_owner"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="">Seleccione un responsable</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= htmlspecialchars($usuario['id_usuario']) ?>"
                            <?= (isset($formData['id_product_owner']) && $formData['id_product_owner'] == $usuario['id_usuario']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($usuario['nombre_completo']) ?> (<?= htmlspecialchars($usuario['nombre_usuario']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($formErrors['id_product_owner'])): ?>
                    <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrors['id_product_owner']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Fechas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="fecha_inicio_planificada" class="block font-semibold text-gray-700 mb-1">Fecha de Inicio</label>
                <input type="date" id="fecha_inicio_planificada" name="fecha_inicio_planificada"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       value="<?= htmlspecialchars($formData['fecha_inicio_planificada'] ?? '') ?>">
                <?php if (isset($formErrors['fecha_inicio_planificada'])): ?>
                    <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrors['fecha_inicio_planificada']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="fecha_fin_planificada" class="block font-semibold text-gray-700 mb-1">Fecha de Fin</label>
                <input type="date" id="fecha_fin_planificada" name="fecha_fin_planificada"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       value="<?= htmlspecialchars($formData['fecha_fin_planificada'] ?? '') ?>">
                <?php if (isset($formErrors['fecha_fin_planificada'])): ?>
                    <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrors['fecha_fin_planificada']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Estado -->
        <div>
            <label for="estado_proyecto" class="block font-semibold text-gray-700 mb-1">Estado del Proyecto <span class="text-red-500">*</span></label>
            <select id="estado_proyecto" name="estado_proyecto" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <option value="Activo" <?= (isset($formData['estado_proyecto']) && $formData['estado_proyecto'] == 'Activo') ? 'selected' : '' ?>>Activo</option>
                <option value="En Pausa" <?= (isset($formData['estado_proyecto']) && $formData['estado_proyecto'] == 'En Pausa') ? 'selected' : '' ?>>En Pausa</option>
                <option value="Completado" <?= (isset($formData['estado_proyecto']) && $formData['estado_proyecto'] == 'Completado') ? 'selected' : '' ?>>Completado</option>
                <option value="Cancelado" <?= (isset($formData['estado_proyecto']) && $formData['estado_proyecto'] == 'Cancelado') ? 'selected' : '' ?>>Cancelado</option>
            </select>
            <?php if (isset($formErrors['estado_proyecto'])): ?>
                <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrors['estado_proyecto']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Botones -->
        <div class="flex justify-between mt-8">
            <a href="index.php?c=Proyecto&a=index" class="inline-flex items-center px-5 py-2 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition">
                <i class="fas fa-arrow-left mr-2"></i> Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center px-6 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow transition">
                <i class="fas fa-save mr-2"></i> <?= $esEditar ? 'Actualizar Proyecto' : 'Crear Proyecto' ?>
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
