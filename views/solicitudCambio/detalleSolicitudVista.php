<?php include __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Detalle Solicitud de Cambio</title>
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
        <p class="whitespace-pre-wrap"><?= htmlspecialchars($sol['descripcion']) ?></p>

        <?php if (!empty($archivos)): ?>
            <h2 class="mt-6 font-semibold">Archivos Adjuntos</h2>
            <ul class="list-disc ml-6">
                <?php foreach ($archivos as $f): ?>
                    <li class="mt-2">
                        <a href="<?= htmlspecialchars($f['ruta_archivo']) ?>"
                           class="text-indigo-600 hover:underline"
                           target="_blank">
                           <?= htmlspecialchars($f['nombre_archivo']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($sol['estado'] === 'Registrada'): ?>
            <form action="index.php?c=SolicitudCambio&a=registrarAnalisis" method="POST" class="mt-6 bg-yellow-50 p-4 rounded">
                <h2 class="font-semibold mb-2">Análisis de Impacto</h2>
                <input type="hidden" name="id_solicitud" value="<?= $sol['id_solicitud'] ?>">
                <textarea name="analisis_impacto" rows="4" class="form-textarea w-full"><?= htmlspecialchars($sol['analisis_impacto'] ?? '') ?></textarea>
                <button type="submit" class="btn-primary mt-2">Guardar Análisis</button>
            </form>
        <?php endif; ?>

        <?php if ($sol['estado'] === 'En Análisis'): ?>
            <form action="index.php?c=SolicitudCambio&a=registrarDecision" method="POST" class="mt-6 bg-green-50 p-4 rounded">
                <h2 class="font-semibold mb-2">Decisión Final</h2>
                <input type="hidden" name="id_solicitud" value="<?= $sol['id_solicitud'] ?>">
                <textarea name="decision_final" rows="3" class="form-textarea w-full"><?= htmlspecialchars($sol['decision_final'] ?? '') ?></textarea>
                <button type="submit" class="btn-primary mt-2">Registrar Decisión</button>
            </form>
        <?php endif; ?>

        <a href="index.php?c=SolicitudCambio&a=index" class="mt-6 inline-block text-blue-600 hover:underline">
            ← Volver al listado
        </a>
    </div>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
