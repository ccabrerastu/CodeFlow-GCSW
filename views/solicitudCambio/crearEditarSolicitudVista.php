<?php
$esEditar = isset($solicitud);
$titulo   = $esEditar ? "Editar Solicitud de Cambio" : "Nueva Solicitud de Cambio";
$action   = $esEditar ? "editar" : "crear";
?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - <?= $titulo ?></title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <h1 class="text-2xl font-bold text-gray-700 mb-6"><?= $titulo ?></h1>

        <?php if (!empty($formErrors['general'])): ?>
            <div class="p-3 mb-4 text-red-700 bg-red-100 rounded-lg">
                <?= htmlspecialchars($formErrors['general']) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?c=SolicitudCambio&a=<?= $action ?>" method="POST" enctype="multipart/form-data">
            <?php if ($esEditar): ?>
                <input type="hidden" name="id_sc" value="<?= $solicitud['id_sc'] ?>">
            <?php endif; ?>

            <div class="mb-4">
                <label for="titulo" class="form-label">Título:</label>
                <input type="text" name="titulo" id="titulo" class="form-input"
                       value="<?= htmlspecialchars($formData['titulo'] ?? $solicitud['titulo'] ?? '') ?>" required>
                <?php if (isset($formErrors['titulo'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['titulo']) ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="form-label">Descripción:</label>
                <textarea name="descripcion" id="descripcion" rows="4" class="form-textarea"><?= htmlspecialchars($formData['descripcion'] ?? $solicitud['descripcion'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
                <label for="archivo" class="form-label">Archivo Adjunto (Opcional):</label>
                <input type="file" name="archivo" id="archivo" class="form-input">
            </div>

            <div class="flex justify-end space-x-4 mt-6">
                <a href="index.php?c=SolicitudCambio&a=listar" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> <?= $esEditar ? 'Actualizar' : 'Crear' ?>
                </button>
            </div>
        </form>
    </div>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
