<?php

$esEditar = isset($accion) && $accion === 'editar';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - <?= htmlspecialchars($tituloPagina) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .btn { padding: 10px 15px; border-radius: 4px; text-decoration: none; display: inline-block; font-size: 0.9rem; }
        .btn-primary { background-color: #4A90E2; color: white; border:none; }
        .btn-primary:hover { background-color: #357ABD; }
        .btn-secondary { background-color: #6c757d; color: white; border:none; }
        .btn-secondary:hover { background-color: #5a6268; }
        .error-message { color: #D0021B; font-size: 0.875em; margin-top: 2px; margin-bottom: 10px; }
    </style>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/partials/header.php'; ?>

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <h1 class="text-2xl font-bold text-gray-700 mb-6"><?= htmlspecialchars($tituloPagina) ?></h1>
        
        <?php if (isset($formErrors['general'])): ?>
            <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <?= htmlspecialchars($formErrors['general']) ?>
            </div>
        <?php endif; ?>

        <form action="<?= $baseUrl ?>index.php?c=Proyecto&a=guardarProyecto" method="POST">
            <?php if ($esEditar && isset($formData['id_proyecto'])): ?>
                <input type="hidden" name="id_proyecto" value="<?= htmlspecialchars($formData['id_proyecto']) ?>">
            <?php endif; ?>

            <div class="mb-4">
                <label for="nombre_proyecto" class="form-label">Nombre del Proyecto:</label>
                <input type="text" name="nombre_proyecto" id="nombre_proyecto" class="form-input" 
                       value="<?= htmlspecialchars($formData['nombre_proyecto'] ?? '') ?>" required>
                <?php if (isset($formErrors['nombre_proyecto'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['nombre_proyecto']) ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea name="descripcion" id="descripcion" rows="4" 
                          class="form-textarea"><?= htmlspecialchars($formData['descripcion'] ?? '') ?></textarea>
                <?php if (isset($formErrors['descripcion'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['descripcion']) ?></p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="id_metodologia" class="form-label">Metodología:</label>
                    <select name="id_metodologia" id="id_metodologia" class="form-select" required>
                        <option value="">Seleccione una metodología</option>
                        <?php foreach ($metodologias as $metodologia): ?>
                            <option value="<?= htmlspecialchars($metodologia['id_metodologia']) ?>" 
                                <?= (isset($formData['id_metodologia']) && $formData['id_metodologia'] == $metodologia['id_metodologia']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($metodologia['nombre_metodologia']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($formErrors['id_metodologia'])): ?>
                        <p class="error-message"><?= htmlspecialchars($formErrors['id_metodologia']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="id_product_owner" class="form-label">Product Owner / Jefe de Proyecto:</label>
                    <select name="id_product_owner" id="id_product_owner" class="form-select">
                        <option value="">Seleccione un responsable</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= htmlspecialchars($usuario['id_usuario']) ?>"
                                <?= (isset($formData['id_product_owner']) && $formData['id_product_owner'] == $usuario['id_usuario']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($usuario['nombre_completo']) ?> (<?= htmlspecialchars($usuario['nombre_usuario']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($formErrors['id_product_owner'])): ?>
                        <p class="error-message"><?= htmlspecialchars($formErrors['id_product_owner']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="fecha_inicio_planificada" class="form-label">Fecha Inicio Planificada:</label>
                    <input type="date" name="fecha_inicio_planificada" id="fecha_inicio_planificada" class="form-input"
                           value="<?= htmlspecialchars($formData['fecha_inicio_planificada'] ?? '') ?>">
                    <?php if (isset($formErrors['fecha_inicio_planificada'])): ?>
                        <p class="error-message"><?= htmlspecialchars($formErrors['fecha_inicio_planificada']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="fecha_fin_planificada" class="form-label">Fecha Fin Planificada:</label>
                    <input type="date" name="fecha_fin_planificada" id="fecha_fin_planificada" class="form-input"
                           value="<?= htmlspecialchars($formData['fecha_fin_planificada'] ?? '') ?>">
                    <?php if (isset($formErrors['fecha_fin_planificada'])): ?>
                        <p class="error-message"><?= htmlspecialchars($formErrors['fecha_fin_planificada']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
             <div class="mb-6">
                <label for="estado_proyecto" class="form-label">Estado del Proyecto:</label>
                <select name="estado_proyecto" id="estado_proyecto" class="form-select" required>
                    <option value="Activo" <?= (isset($formData['estado_proyecto']) && $formData['estado_proyecto'] == 'Activo') ? 'selected' : '' ?>>Activo</option>
                    <option value="En Pausa" <?= (isset($formData['estado_proyecto']) && $formData['estado_proyecto'] == 'En Pausa') ? 'selected' : '' ?>>En Pausa</option>
                    <option value="Completado" <?= (isset($formData['estado_proyecto']) && $formData['estado_proyecto'] == 'Completado') ? 'selected' : '' ?>>Completado</option>
                    <option value="Cancelado" <?= (isset($formData['estado_proyecto']) && $formData['estado_proyecto'] == 'Cancelado') ? 'selected' : '' ?>>Cancelado</option>
                </select>
                 <?php if (isset($formErrors['estado_proyecto'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['estado_proyecto']) ?></p>
                <?php endif; ?>
            </div>


            <div class="flex items-center justify-end space-x-4 mt-6">
                <a href="index.php?c=Proyecto&a=index" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> <?= $esEditar ? 'Actualizar Proyecto' : 'Crear Proyecto' ?>
                </button>
            </div>
        </form>
    </div>

    <?php include __DIR__ . '/partials/footer.php';  ?>
</body>
</html>
