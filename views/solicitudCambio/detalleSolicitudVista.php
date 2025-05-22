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

        <a href="index.php?c=SolicitudCambio&a=index" class="mt-6 inline-block text-blue-600 hover:underline">
            ← Volver al listado
        </a>
    </div>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
