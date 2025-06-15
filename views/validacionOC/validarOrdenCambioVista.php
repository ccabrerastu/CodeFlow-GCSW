<?php include __DIR__ . '/../partials/header.php'; ?>

<?php
    $formDataVal   = $_SESSION['form_data_val']   ?? [];
    $formErrorsVal = $_SESSION['form_errors_val'] ?? [];
    unset($_SESSION['form_data_val'], $_SESSION['form_errors_val']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Validar Orden de Cambio #<?= htmlspecialchars($orden['id_orden']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
    <body class="bg-gray-100">
        <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
            <h1 class="text-2xl font-bold text-gray-700 mb-6">
                Validar Orden #<?= htmlspecialchars($orden['id_orden']) ?>
            </h1>

            <div class="mb-4">
                <p><strong>Título:</strong> <?= htmlspecialchars($orden['titulo_solicitud']) ?></p>
                <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($orden['descripcion'])) ?></p>
                <p><strong>Estado actual:</strong> <?= htmlspecialchars($orden['estado']) ?></p>
            </div>

            <?php if (!empty($formErrorsVal['general'])): ?>
                <div class="p-3 mb-4 text-red-700 bg-red-100 rounded-lg">
                    <?= htmlspecialchars($formErrorsVal['general']) ?>
                </div>
            <?php endif; ?>

            <form action="index.php?c=ValidacionOC&a=validar" method="POST" class="mt-8 bg-purple-50 p-4 rounded border">
                <input type="hidden" name="id_orden" value="<?= htmlspecialchars($orden['id_orden']) ?>">

                <div class="mb-4">
                    <label for="resultado_validacion" class="form-label font-medium">Resultado:</label>
                    <select name="resultado_validacion" id="resultado_validacion" class="form-select w-full" required>
                        <option value="">-- Seleccionar --</option>
                        <option value="1" <?= ($formDataVal['resultado_validacion'] ?? '') === '1' ? 'selected' : '' ?>>
                            Aprobado
                        </option>
                        <option value="0" <?= ($formDataVal['resultado_validacion'] ?? '') === '0' ? 'selected' : '' ?>>
                            Rechazado
                        </option>
                    </select>
                    <?php if (!empty($formErrorsVal['resultado_validacion'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($formErrorsVal['resultado_validacion']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <label for="comentarios_validacion" class="form-label font-medium">Comentarios justificativos:</label>
                    <textarea name="comentarios_validacion" id="comentarios_validacion" rows="4"
                            class="form-textarea w-full" required><?= htmlspecialchars($formDataVal['comentarios_validacion'] ?? '') ?></textarea>
                    <?php if (!empty($formErrorsVal['comentarios_validacion'])): ?>
                        <div class="text-red-600 text-sm"><?= htmlspecialchars($formErrorsVal['comentarios_validacion']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="index.php?c=OrdenCambio&a=index" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check mr-1"></i> Enviar Validación
                    </button>
                </div>
            </form>
        </div>
        <?php include __DIR__ . '/../partials/footer.php'; ?>
    </body>
</html>