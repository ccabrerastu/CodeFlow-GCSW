<?php
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"/>
</head>
<body class="bg-gray-100 min-h-screen">

<?php include __DIR__ . '/partials/header.php'; ?>

<div class="max-w-2xl mx-auto mt-12 px-6 py-8 bg-white rounded-2xl shadow-lg">
    <h1 class="text-2xl font-bold text-gray-800 mb-6"><?= $tituloPagina ?></h1>

    <?php if (isset($formErrors['general'])): ?>
        <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 border border-red-200 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($formErrors['general']) ?>
        </div>
    <?php endif; ?>

    <form action="<?= $baseUrl . $urlAccion ?>" method="POST" class="space-y-6">
        <?php if ($esEditar && isset($formData['id_fase_metodologia'])): ?>
            <input type="hidden" name="id_fase_metodologia" value="<?= htmlspecialchars($formData['id_fase_metodologia']) ?>">
        <?php endif; ?>
        <input type="hidden" name="id_metodologia" value="<?= htmlspecialchars($metodologia['id_metodologia']) ?>">

        <!-- Nombre de la fase -->
        <div>
            <label for="nombre_fase" class="block text-sm font-semibold text-gray-700 mb-1">Nombre de la Fase <span class="text-red-500">*</span></label>
            <input type="text" id="nombre_fase" name="nombre_fase" required
                   class="w-full border border-gray-300 rounded-md px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   value="<?= htmlspecialchars($formData['nombre_fase'] ?? '') ?>">
            <?php if (isset($formErrors['nombre_fase'])): ?>
                <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrors['nombre_fase']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Descripción -->
        <div>
            <label for="descripcion" class="block text-sm font-semibold text-gray-700 mb-1">Descripción (opcional)</label>
            <textarea id="descripcion" name="descripcion" rows="3"
                      class="w-full border border-gray-300 rounded-md px-4 py-2 shadow-sm resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($formData['descripcion'] ?? '') ?></textarea>
            <?php if (isset($formErrors['descripcion'])): ?>
                <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrors['descripcion']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Orden -->
        <div>
            <label for="orden" class="block text-sm font-semibold text-gray-700 mb-1">Orden (numérico) <span class="text-red-500">*</span></label>
            <input type="number" id="orden" name="orden" min="0" required
                   class="w-full border border-gray-300 rounded-md px-4 py-2 shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                   value="<?= htmlspecialchars($formData['orden'] ?? '0') ?>">
            <?php if (isset($formErrors['orden'])): ?>
                <p class="text-sm text-red-600 mt-1"><?= htmlspecialchars($formErrors['orden']) ?></p>
            <?php endif; ?>
        </div>

        <!-- Botones -->
        <div class="flex justify-end items-center gap-4">
            <a href="index.php?c=FasesMetodologia&a=listarPorMetodologia&id_metodologia=<?= htmlspecialchars($metodologia['id_metodologia']) ?>"
               class="inline-flex items-center px-4 py-2 rounded-md text-sm font-medium text-gray-600 bg-gray-200 hover:bg-gray-300 transition">
                <i class="fas fa-arrow-left mr-2"></i> Cancelar
            </a>
            <button type="submit"
                    class="inline-flex items-center px-5 py-2 rounded-md text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition shadow">
                <i class="fas fa-save mr-2"></i> <?= $esEditar ? 'Actualizar Fase' : 'Crear Fase' ?>
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
