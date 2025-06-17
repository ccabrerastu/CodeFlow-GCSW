<?php include __DIR__ . '/../partials/header.php'; ?>

<?php
$formData   = $_SESSION['form_data_solicitud']   ?? [];
$formErrors = $_SESSION['form_errors_solicitud'] ?? [];
unset($_SESSION['form_data_solicitud'], $_SESSION['form_errors_solicitud']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC – Detalle Solicitud de Cambio</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow">
        <h1 class="text-2xl font-bold mb-4">
            Solicitud #<?= htmlspecialchars($sol['id_solicitud']) ?> –
            <?= htmlspecialchars($sol['titulo']) ?>
        </h1>
        <p><strong>Proyecto:</strong> <?= htmlspecialchars($sol['nombre_proyecto']) ?></p>
        <p><strong>Solicitante:</strong> <?= htmlspecialchars($sol['nombre_completo']) ?></p>
        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($sol['fecha_creacion'])) ?></p>
        <p><strong>Estado:</strong> <?= htmlspecialchars($sol['estado']) ?></p>

        <h2 class="mt-6 font-semibold">Descripción detallada</h2>
        <p class="whitespace-pre-wrap"><?= nl2br(htmlspecialchars($sol['descripcion'])) ?></p>

        <?php if (!empty($sol['analisis_impacto'])): ?>
            <div class="mt-6 bg-gray-50 p-4 rounded border">
                <h2 class="font-semibold mb-2">Análisis de Impacto registrado</h2>
                <p class="whitespace-pre-wrap"><?= nl2br(htmlspecialchars($sol['analisis_impacto'])) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($sol['estado'] === 'Registrada'): ?>
            <form action="index.php?c=SolicitudCambio&a=registrarAnalisis" method="POST"
                  class="mt-6 bg-yellow-50 p-4 rounded border">
                <h2 class="font-semibold mb-2">Registrar Análisis de Impacto</h2>
                <input type="hidden" name="id_solicitud"
                       value="<?= $sol['id_solicitud'] ?>">
                <textarea name="analisis_impacto" rows="4"
                          class="form-textarea w-full"
                          placeholder="Describe el análisis..."><?= htmlspecialchars($formData['analisis_impacto'] ?? $sol['analisis_impacto'] ?? '') ?></textarea>
                <?php if (!empty($formErrors['analisis_impacto'])): ?>
                    <div class="text-red-600 text-sm"><?= htmlspecialchars($formErrors['analisis_impacto']) ?></div>
                <?php endif; ?>
                <button type="submit" class="btn-primary mt-2">Guardar Análisis</button>
            </form>
        <?php endif; ?>

        <?php if ($sol['estado'] === 'En Análisis'): ?>
            <form action="index.php?c=SolicitudCambio&a=registrarDecision" method="POST"
                  class="mt-6 bg-green-50 p-4 rounded border">
                <h2 class="font-semibold mb-2">Registrar Decisión Final</h2>
                <input type="hidden" name="id_solicitud"
                       value="<?= $sol['id_solicitud'] ?>">

                <?php
                $selEstado = $formData['estado_sc'] ?? '';
                ?>

                <label class="form-label font-medium">Decisión:</label>
                <select name="estado_sc" class="form-select w-full mb-4" required>
                    <option value="">-- Seleccione --</option>
                    <option value="Aprobada"  <?= $selEstado==='Aprobada'  ? 'selected':'' ?>>Aprobada</option>
                    <option value="Rechazada" <?= $selEstado==='Rechazada' ? 'selected':'' ?>>Rechazada</option>
                </select>
                <?php if (!empty($formErrors['estado_sc'])): ?>
                    <div class="text-red-600 text-sm mb-4"><?= htmlspecialchars($formErrors['estado_sc']) ?></div>
                <?php endif; ?>

                <label class="form-label font-medium">Comentario justificativo:</label>
                <textarea name="decision_final" rows="4"
                          class="form-textarea w-full mb-4"
                          placeholder="Escribe aquí tu comentario..."><?= htmlspecialchars($formData['decision_final'] ?? '') ?></textarea>
                <?php if (!empty($formErrors['decision_final'])): ?>
                    <div class="text-red-600 text-sm"><?= htmlspecialchars($formErrors['decision_final']) ?></div>
                <?php endif; ?>

                <button type="submit" class="btn-primary mt-2">Guardar Decisión</button>
            </form>
        <?php endif; ?>

        <?php if (!empty($sol['decision_final'])): ?>
            <div class="mt-6 bg-gray-50 p-4 rounded border">
                <h2 class="font-semibold mb-2">Decisión Final registrada</h2>
                <p class="whitespace-pre-wrap"><?= nl2br(htmlspecialchars($sol['decision_final'])) ?></p>
            </div>
        <?php endif; ?>

        <?php if ($sol['estado'] === 'Aprobada'): ?>
            <a href="index.php?c=OrdenCambio&a=mostrarFormularioCrear&id=<?= $sol['id_solicitud'] ?>"
               class="btn-primary mt-4 inline-block">
               ← Convertir en Orden de Cambio
            </a>
        <?php endif; ?>

<?php if (!empty($archivos)): ?>
  <div class="mt-6 bg-gray-50 p-4 rounded border">
    <h2 class="font-semibold mb-2">Archivos Adjuntos:</h2>
    <ul class="list-disc ml-6">
      <?php foreach ($archivos as $file):
          $idAdj = (int) $file['id_adjunto_sc'];
      ?>
        <li>
          <a 
            href="index.php?c=SolicitudCambio&a=descargarArchivo&id=<?= $idAdj ?>" 
            class="text-indigo-600 hover:underline"
          >
            <?= htmlspecialchars($file['nombre_archivo']) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

        <a href="index.php?c=SolicitudCambio&a=index"
           class="mt-6 inline-block text-blue-600 hover:underline">
           ← Volver al listado
        </a>
    </div>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
