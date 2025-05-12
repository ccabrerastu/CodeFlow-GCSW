<?php
// Asume que $baseUrl, $metodologia, $fase (si es editar), $formData, $formErrors, y $accion ('crear' o 'editar') están disponibles.
// include __DIR__ . '/partials/header.php';

$esEditar = isset($accion) && $accion === 'editar';
$tituloPagina = $esEditar ? "Editar Fase de " . htmlspecialchars($metodologia['nombre_metodologia']) : "Nueva Fase para " . htmlspecialchars($metodologia['nombre_metodologia']);
$urlAccion = $esEditar ? "index.php?c=FasesMetodologia&a=editar" : "index.php?c=FasesMetodologia&a=crear";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - <?= $tituloPagina ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 700px; margin: 20px auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .btn { padding: 10px 15px; border-radius: 4px; text-decoration: none; display: inline-block; font-size: 0.9rem; }
        .btn-primary { background-color: #4A90E2; color: white; border:none; }
        .btn-primary:hover { background-color: #357ABD; }
        .btn-secondary { background-color: #6c757d; color: white; border:none; }
        .btn-secondary:hover { background-color: #5a6268; }
        .error-message { color: #D0021B; font-size: 0.875em; margin-top: -10px; margin-bottom: 10px; }
    </style>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/partials/header.php'; ?>

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <h1 class="text-2xl font-bold text-gray-700 mb-6"><?= $tituloPagina ?></h1>
        
        <?php if (isset($formErrors['general'])): ?>
            <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <?= htmlspecialchars($formErrors['general']) ?>
            </div>
        <?php endif; ?>

        <form action="<?= $baseUrl . $urlAccion ?>" method="POST">
            <?php if ($esEditar && isset($formData['id_fase_metodologia'])): ?>
                <input type="hidden" name="id_fase_metodologia" value="<?= htmlspecialchars($formData['id_fase_metodologia']) ?>">
            <?php endif; ?>
            <input type="hidden" name="id_metodologia" value="<?= htmlspecialchars($metodologia['id_metodologia']) ?>">

            <div class="mb-4">
                <label for="nombre_fase" class="form-label">Nombre de la Fase:</label>
                <input type="text" name="nombre_fase" id="nombre_fase" class="form-input" 
                       value="<?= htmlspecialchars($formData['nombre_fase'] ?? '') ?>" required>
                <?php if (isset($formErrors['nombre_fase'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['nombre_fase']) ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label for="descripcion" class="form-label">Descripción (Opcional):</label>
                <textarea name="descripcion" id="descripcion" rows="3" 
                          class="form-input"><?= htmlspecialchars($formData['descripcion'] ?? '') ?></textarea>
                <?php if (isset($formErrors['descripcion'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['descripcion']) ?></p>
                <?php endif; ?>
            </div>

            <div class="mb-6">
                <label for="orden" class="form-label">Orden (Numérico):</label>
                <input type="number" name="orden" id="orden" class="form-input" 
                       value="<?= htmlspecialchars($formData['orden'] ?? '0') ?>" min="0" required>
                <?php if (isset($formErrors['orden'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['orden']) ?></p>
                <?php endif; ?>
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="index.php?c=FasesMetodologia&a=listarPorMetodologia&id_metodologia=<?= htmlspecialchars($metodologia['id_metodologia']) ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> <?= $esEditar ? 'Actualizar Fase' : 'Crear Fase' ?>
                </button>
            </div>
        </form>
    </div>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
