<?php
$esEditar      = !empty($formData['id_solicitud']);
$tituloPagina  = $esEditar ? 'Editar Solicitud de Cambio' : 'Nueva Solicitud de Cambio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - <?= htmlspecialchars($tituloPagina) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-6"><?= htmlspecialchars($tituloPagina) ?></h1>

        <?php if (!empty($formErrors['general'])): ?>
            <div class="p-3 mb-4 text-red-700 bg-red-100 rounded">
                <?= htmlspecialchars($formErrors['general']) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?c=SolicitudCambio&a=<?= $esEditar ? 'editar' : 'crear' ?>" method="POST" class="space-y-4" enctype="multipart/form-data">
            <?php if ($esEditar): ?>
                <input type="hidden" name="id_solicitud" value="<?= htmlspecialchars($formData['id_solicitud']) ?>">
            <?php endif; ?>

            <div>
                <label for="id_proyecto" class="block font-bold mb-1">Proyecto:</label>
                <select name="id_proyecto" id="id_proyecto" class="w-full border p-2 rounded" required>
                    <option value="">-- Seleccione un proyecto --</option>
                    <?php foreach ($proyectos as $p): ?>
                        <option value="<?= $p['id_proyecto'] ?>"
                            <?= ($formData['id_proyecto'] == $p['id_proyecto']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nombre_proyecto']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($formErrors['id_proyecto'])): ?>
                    <p class="text-red-600 text-sm mt-1"><?= htmlspecialchars($formErrors['id_proyecto']) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="prioridad" class="form-label">Prioridad:</label>
                <select name="prioridad" id="prioridad" class="form-select" required>
                    <option value="">-- Seleccione prioridad --</option>
                    <?php foreach (['ALTA','MEDIA','BAJA'] as $p): ?>
                        <option value="<?= $p ?>"
                            <?= (isset($formData['prioridad']) && $formData['prioridad'] === $p) ? 'selected' : '' ?>>
                            <?= ucfirst(strtolower($p)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($formErrors['prioridad'])): ?>
                    <div class="error-message"><?= htmlspecialchars($formErrors['prioridad']) ?></div>
                <?php endif; ?>
            </div>
            <div>
                <label for="tipo_cambio" class="form-label">Tipo de Cambio:</label>
                <select name="tipo_cambio" id="tipo_cambio" class="form-select" required>
                    <option value="">-- Seleccionar tipo --</option>
                    <?php foreach(['CORRECCION','MEJORA','NUEVA_FUNCIONALIDAD'] as $tc): ?>
                        <option value="<?= $tc ?>"
                            <?= (isset($formData['tipo_cambio']) && $formData['tipo_cambio']===$tc) ? 'selected':'' ?>>
                            <?= $tc ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($formErrors['tipo_cambio'])): ?>
                    <div class="error-message"><?= htmlspecialchars($formErrors['tipo_cambio']) ?></div>
                <?php endif; ?>
            </div>
            <div>
                <label for="justificacion" class="form-label">Justificación:</label>
                <textarea name="justificacion" id="justificacion" rows="3"
                        class="form-textarea"><?= htmlspecialchars($formData['justificacion'] ?? '') ?></textarea>
                <?php if (!empty($formErrors['justificacion'])): ?>
                    <div class="error-message"><?= htmlspecialchars($formErrors['justificacion']) ?></div>
                <?php endif; ?>
            </div>
            <div>
                <label for="titulo" class="block font-bold mb-1">Título:</label>
                <input type="text" name="titulo" id="titulo" class="w-full border p-2 rounded"
                       value="<?= htmlspecialchars($formData['titulo'] ?? '') ?>" required>
                <?php if (!empty($formErrors['titulo'])): ?>
                    <p class="text-red-600 text-sm mt-1"><?= htmlspecialchars($formErrors['titulo']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="descripcion" class="block font-bold mb-1">Descripción:</label>
                <textarea name="descripcion" id="descripcion" rows="4"
                          class="w-full border p-2 rounded" required><?= htmlspecialchars($formData['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="flex items-center space-x-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                    <?= $esEditar ? 'Actualizar' : 'Crear' ?>
                </button>
                <a href="index.php?c=SolicitudCambio&a=index" class="text-gray-600 hover:underline">Volver a la lista</a>
            </div>
            <div>
                <label class="form-label">Adjuntar justificantes:</label>
                <input type="file" name="archivos[]" multiple class="form-input" />
            </div>
        </form>
    </div>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
