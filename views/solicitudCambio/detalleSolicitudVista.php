<?php include __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Detalle Solicitud de Cambio</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <h1 class="text-2xl font-bold text-gray-700 mb-4">
            Solicitud #<?= $solicitud['id_sc'] ?> – <?= htmlspecialchars($solicitud['titulo']) ?>
        </h1>
        <p class="mb-2"><strong>Solicitante:</strong> <?= htmlspecialchars($solicitud['nombre_usuario']) ?></p>
        <p class="mb-2"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($solicitud['fecha_creacion'])) ?></p>
        <p class="mb-4"><strong>Estado:</strong> <?= htmlspecialchars($solicitud['estado_sc']) ?></p>

        <div class="mb-6">
            <h2 class="font-semibold text-gray-800">Descripción:</h2>
            <p class="text-gray-700"><?= nl2br(htmlspecialchars($solicitud['descripcion'])) ?></p>
        </div>

        <?php if (!empty($archivos)): ?>
            <div class="mb-6">
                <h2 class="font-semibold text-gray-800 mb-2">Archivos Adjuntos:</h2>
                <ul class="list-disc ml-6">
                    <?php foreach ($archivos as $file): ?>
                        <li>
                            <a href="<?= $file['ruta'] ?>" class="text-indigo-600 hover:underline" target="_blank">
                                <?= htmlspecialchars($file['nombre_archivo']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <a href="index.php?c=SolicitudCambio&a=listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
    </div>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
