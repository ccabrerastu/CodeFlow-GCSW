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
<div class="container mx-auto mt-10 p-6 bg-white rounded-xl shadow-lg ring-1 ring-gray-200 space-y-6">

    <!-- Encabezado -->
<div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border border-gray-200 rounded-2xl shadow-md p-6 mb-6">
    <div class="flex items-center gap-3 mb-4">
        <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
            <i class="fas fa-file-signature fa-lg"></i>
        </div>
        <h1 class="text-3xl font-extrabold text-gray-800">
            Orden #<?= htmlspecialchars($orden['id_orden']) ?>
            <span class="block text-lg font-medium text-gray-600">"<?= htmlspecialchars($orden['titulo_solicitud']) ?>"</span>
        </h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm text-gray-800">
        <div class="flex items-start gap-2">
            <i class="fas fa-user text-indigo-500 mt-1"></i>
            <p><strong class="text-gray-900">Creada por:</strong> <?= htmlspecialchars($orden['nombre_creador']) ?></p>
        </div>

        <div class="flex items-start gap-2">
            <i class="fas fa-calendar-alt text-pink-500 mt-1"></i>
            <p><strong class="text-gray-900">Fecha de creación:</strong> <?= date('d/m/Y H:i', strtotime($orden['fecha_creacion'])) ?></p>
        </div>

        <div class="flex items-start gap-2">
            <i class="fas fa-flag text-yellow-600 mt-1"></i>
            <p>
                <strong class="text-gray-900">Estado:</strong>
                <span class="ml-1 px-3 py-1 inline-block text-xs font-bold rounded-full 
                    <?= match(strtolower($orden['estado'])) {
                        'terminado' => 'bg-green-100 text-green-700',
                        'aprobada' => 'bg-blue-100 text-blue-700',
                        'rechazada' => 'bg-red-100 text-red-700',
                        default => 'bg-gray-100 text-gray-600'
                    } ?>">
                    <?= htmlspecialchars($orden['estado']) ?>
                </span>
            </p>
        </div>

        <div class="flex items-start gap-2">
            <i class="fas fa-hourglass-start text-orange-500 mt-1"></i>
            <p><strong class="text-gray-900">Inicio ejecución planificado:</strong>
                <?= $orden['fecha_inicio_planificada'] ? date('d/m/Y', strtotime($orden['fecha_inicio_planificada'])) : '—' ?>
            </p>
        </div>

        <div class="flex items-start gap-2">
            <i class="fas fa-hourglass-end text-purple-600 mt-1"></i>
            <p><strong class="text-gray-900">Fin ejecución planificado:</strong>
                <?= $orden['fecha_fin_planificada'] ? date('d/m/Y', strtotime($orden['fecha_fin_planificada'])) : '—' ?>
            </p>
        </div>
    </div>
</div>
    <!-- Descripción -->
<!-- Descripción -->
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2 mb-2">
        <i class="fas fa-align-left text-indigo-500"></i> Descripción
    </h2>
    <div class="bg-indigo-50 border border-indigo-200 p-4 rounded-lg shadow-sm text-gray-700">
        <?= nl2br(htmlspecialchars($orden['descripcion'])) ?>
    </div>
</div>
<!-- Formulario de Seguimiento -->
<?php if (!in_array($orden['estado'], ['Terminado', 'Aprobada', 'Rechazada'])): ?>
    <section class="mb-12">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-sync-alt text-blue-600 text-xl"></i>
            <h2 class="text-2xl font-extrabold text-gray-800">Actualizar Estado de la Orden</h2>
        </div>

        <form action="index.php?c=OrdenCambio&a=registrarSeguimiento" method="POST"
              class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-2xl p-6 shadow-md space-y-6">
            
            <input type="hidden" name="id_orden" value="<?= $orden['id_orden'] ?>">

            <!-- Nuevo Estado -->
            <div class="mb-4">
                <label for="nuevo_estado" class="block text-base font-semibold text-blue-800 mb-2">
                    <i class="fas fa-exchange-alt mr-1 text-blue-600"></i> Nuevo Estado <span class="text-red-500">*</span>
                </label>
                <select name="nuevo_estado" id="nuevo_estado"
                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                        required>
                    <option value="">— Seleccione —</option>
                    <option value="En Proceso" <?= ($formDataSeg['nuevo_estado'] ?? '') === 'En Proceso' ? 'selected' : '' ?>>
                        🟡 En Proceso
                    </option>
                    <option value="Terminado" <?= ($formDataSeg['nuevo_estado'] ?? '') === 'Terminado' ? 'selected' : '' ?>>
                        ✅ Terminado
                    </option>
                </select>

                <?php if (!empty($formErrorsSeg['nuevo_estado'])): ?>
                    <p class="text-sm text-red-600 mt-2"><?= htmlspecialchars($formErrorsSeg['nuevo_estado']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Comentario -->
            <div class="mb-4">
                <label for="comentario" class="block text-base font-semibold text-blue-900 mb-2">
                    <i class="fas fa-comment-alt text-blue-600 mr-1"></i> Comentario (opcional)
                </label>
                <textarea name="comentario" id="comentario" rows="4"
                        placeholder="Agrega un detalle adicional si lo consideras necesario..."
                        class="w-full px-4 py-2 border border-gray-300 bg-white rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"><?= htmlspecialchars($formDataSeg['comentario'] ?? '') ?></textarea>
            </div>

            <!-- Botón -->
            <div class="text-right">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg shadow transition duration-200">
                    <i class="fas fa-save"></i> Guardar Seguimiento
                </button>
            </div>
        </form>
    </section>
<?php endif; ?>


<!-- Historial de Seguimiento -->
<?php if (!empty($comentarios)): ?>
    <div>
        <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2 mb-4">
            <i class="fas fa-history text-purple-500"></i> Historial de Seguimiento
        </h2>
        <ul class="space-y-4">
            <?php foreach ($comentarios as $c): ?>
                <li class="bg-white border border-gray-200 rounded-lg shadow-sm p-4">
                    <div class="flex justify-between text-sm text-gray-600">
                        <span><strong><?= htmlspecialchars($c['nombre_completo']) ?></strong></span>
                        <span><?= date('d/m/Y H:i', strtotime($c['fecha_comentario'])) ?></span>
                    </div>
                    <p class="text-gray-800 mt-2 whitespace-pre-wrap"><?= nl2br(htmlspecialchars($c['comentario'])) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

    <!-- Validación QA -->
    <?php if ($orden['estado'] === 'Terminado'): ?>
        <form action="index.php?c=ValidacionOC&a=validar" method="POST"
              class="bg-purple-50 p-5 rounded-lg border border-purple-200 space-y-4">
            <h2 class="text-xl font-semibold text-purple-900">✅ Validación QA</h2>
            <input type="hidden" name="id_orden" value="<?= $orden['id_orden'] ?>">

        <div class="mb-4">
            <label for="decision" class="block text-sm font-semibold text-purple-800 mb-1">
                <i class="fas fa-clipboard-check mr-1 text-purple-500"></i> Resultado de Validación:
            </label>
            <select
                id="decision"
                name="decision"
                class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                required
            >
                <option value="">-- Seleccione --</option>
                <option value="Aprobada" <?= ($formDataVal['decision'] ?? '') === 'Aprobada' ? 'selected' : '' ?>>✅ Aprobada</option>
                <option value="Rechazada" <?= ($formDataVal['decision'] ?? '') === 'Rechazada' ? 'selected' : '' ?>>❌ Rechazada</option>
            </select>

            <?php if (!empty($formErrorsVal['decision'])): ?>
                <p class="text-red-600 text-xs mt-1 italic">
                    <?= htmlspecialchars($formErrorsVal['decision']) ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="mb-4">
            <label for="comentario" class="block text-sm font-semibold text-purple-800 mb-1">
                <i class="fas fa-comment-dots mr-1 text-purple-500"></i> Comentarios Justificativos:
            </label>
            <textarea
                id="comentario"
                name="comentario"
                rows="4"
                class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                placeholder="Explique brevemente el motivo de su decisión..."><?= htmlspecialchars($formDataVal['comentario'] ?? '') ?></textarea>

            <?php if (!empty($formErrorsVal['comentario'])): ?>
                <p class="text-red-600 text-xs mt-1 italic">
                    <?= htmlspecialchars($formErrorsVal['comentario']) ?>
                </p>
            <?php endif; ?>
        </div>

            <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                Confirmar Validación
            </button>
        </form>
    <?php endif; ?>

    <!-- Volver -->
<div class="pt-6">
    <a href="index.php?c=OrdenCambio&a=index"
       class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 rounded-lg shadow-sm text-sm font-medium transition">
        <i class="fas fa-arrow-left mr-2"></i> Volver al listado
    </a>
</div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
