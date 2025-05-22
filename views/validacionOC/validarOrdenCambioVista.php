<?php include __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Validar Orden de Cambio</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <h1 class="text-2xl font-bold text-gray-700 mb-6">Validar Orden #<?= $orden['id_oc'] ?></h1>

        <div class="mb-4">
            <p><strong>Título:</strong> <?= htmlspecialchars($orden['titulo_oc']) ?></p>
            <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($orden['descripcion_oc'])) ?></p>
            <p><strong>Estado actual:</strong> <?= htmlspecialchars($orden['estado_oc']) ?></p>
        </div>

        <?php if (!empty($formErrors['general'])): ?>
            <div class="p-3 mb-4 text-red-700 bg-red-100 rounded-lg">
                <?= htmlspecialchars($formErrors['general']) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?c=ValidacionesOC&a=validar" method="POST">
            <input type="hidden" name="id_oc" value="<?= $orden['id_oc'] ?>">

            <div class="mb-4">
                <label for="estado_validacion" class="form-label">Resultado:</label>
                <select name="estado_validacion" id="estado_validacion" class="form-select" required>
                    <option value="">-- Seleccionar --</option>
                    <option value="Aprobada">Aprobada</option>
                    <option value="Rechazada">Rechazada</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="comentario_validacion" class="form-label">Comentarios:</label>
                <textarea name="comentario_validacion" id="comentario_validacion" rows="4" class="form-textarea"></textarea>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="index.php?c=OrdenCambio&a=listar" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check mr-1"></i> Enviar Validación
                </button>
            </div>
        </form>
    </div>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
