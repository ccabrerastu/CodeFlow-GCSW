<?php include __DIR__ . '/../partials/header.php'; ?>

<?php
    $formDataSeg   = $_SESSION['form_data_seguimiento'] ?? [];
    $formErrorsSeg = $_SESSION['form_errors_seguimiento'] ?? [];
    unset($_SESSION['form_data_seguimiento'], $_SESSION['form_errors_seguimiento']);

    $formDataVal   = $_SESSION['form_data_validacion']   ?? [];
    $formErrorsVal = $_SESSION['form_errors_validacion'] ?? [];
    unset($_SESSION['form_data_validacion'], $_SESSION['form_errors_validacion']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC – Detalle Orden de Cambio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-4">
        Orden #<?= htmlspecialchars($orden['id_orden']) ?> –
        <?= htmlspecialchars($orden['titulo_solicitud']) ?>
    </h1>

    <p><strong>Creada por:</strong> <?= htmlspecialchars($orden['nombre_creador']) ?></p>
    <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($orden['fecha_creacion'])) ?></p>
    <p><strong>Estado:</strong> <?= htmlspecialchars($orden['estado']) ?></p>

    <h2 class="mt-6 font-semibold">Descripción</h2>
    <p class="whitespace-pre-wrap"><?= nl2br(htmlspecialchars($orden['descripcion'])) ?></p>

    <?php if (!in_array($orden['estado'], ['Terminado','Aprobada','Rechazada'])): ?>
        <form action="index.php?c=OrdenCambio&a=registrarSeguimiento" method="POST"
              class="mt-6 bg-blue-50 p-4 rounded border">
            <input type="hidden" name="id_orden" value="<?= $orden['id_orden'] ?>">

            <div>
                <label class="form-label font-medium">Nuevo Estado:</label>
                <select name="nuevo_estado" class="form-select w-full" required>
                    <option value="">-- Seleccione --</option>
                    <option value="En Proceso"
                        <?= ( $formDataSeg['nuevo_estado'] ?? '' ) === 'En Proceso' ? 'selected':'' ?>>
                        En Proceso
                    </option>
                    <option value="Terminado"
                        <?= ( $formDataSeg['nuevo_estado'] ?? '' ) === 'Terminado' ? 'selected':'' ?>>
                        Terminado
                    </option>
                </select>
                <?php if (!empty($formErrorsSeg['nuevo_estado'])): ?>
                    <div class="text-red-600 text-sm"><?= htmlspecialchars($formErrorsSeg['nuevo_estado']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mt-4">
                <label class="form-label font-medium">Comentario (opcional):</label>
                <textarea name="comentario" rows="3"
                          class="form-textarea w-full"><?= htmlspecialchars($formDataSeg['comentario'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="btn-primary mt-3">Guardar Seguimiento</button>
        </form>
    <?php endif; ?>

    <?php if (!empty($comentarios)): ?>
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-2">Historial de Seguimiento</h2>
            <ul class="space-y-4">
                <?php foreach($comentarios as $c): ?>
                    <li class="p-4 bg-gray-50 rounded border">
                        <p class="text-sm text-gray-600">
                            <strong><?= htmlspecialchars($c['nombre_completo']) ?></strong>
                            – <?= date('d/m/Y H:i', strtotime($c['fecha_comentario'])) ?>
                        </p>
                        <p class="whitespace-pre-wrap"><?= nl2br(htmlspecialchars($c['comentario'])) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($orden['estado'] === 'Terminado'): ?>
        <form action="index.php?c=ValidacionOC&a=validar" method="POST"
            class="mt-8 bg-purple-50 p-4 rounded border">
            <h2 class="text-xl font-semibold mb-2">Validación QA</h2>
            <input type="hidden" name="id_orden" value="<?= $orden['id_orden'] ?>">

            <div class="mb-4">
                <label class="form-label font-medium">Resultado:</label>
                <select name="decision" class="form-select w-full" required>
                    <option value="">-- Seleccione --</option>
                    <option value="Aprobada"
                        <?= ($formDataVal['decision'] ?? '') === 'Aprobada' ? 'selected':'' ?>>
                        Aprobada
                    </option>
                    <option value="Rechazada"
                        <?= ($formDataVal['decision'] ?? '') === 'Rechazada' ? 'selected':'' ?>>
                        Rechazada
                    </option>
                </select>
                <?php if (!empty($formErrorsVal['decision'])): ?>
                    <div class="text-red-600 text-sm"><?= htmlspecialchars($formErrorsVal['decision']) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-4">
                <label class="form-label font-medium">Comentarios justificativos:</label>
                <textarea name="comentario" rows="4"
                        class="form-textarea w-full"
                        placeholder="Obligatorio"><?= htmlspecialchars($formDataVal['comentario'] ?? '') ?></textarea>
                <?php if (!empty($formErrorsVal['comentario'])): ?>
                    <div class="text-red-600 text-sm"><?= htmlspecialchars($formErrorsVal['comentario']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-primary">Confirmar Validación</button>
        </form>
    <?php endif; ?>

    <a href="index.php?c=OrdenCambio&a=index"
       class="mt-6 inline-block text-blue-600 hover:underline">
       ← Volver al listado
    </a>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
