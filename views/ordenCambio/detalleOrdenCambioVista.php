<?php include __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Detalle Orden de Cambio</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <h1 class="text-2xl font-bold text-gray-700 mb-4">
            Orden #<?= $orden['id_oc'] ?> – <?= htmlspecialchars($orden['titulo_oc']) ?>
        </h1>
        <p class="mb-2"><strong>Creada por:</strong> <?= htmlspecialchars($orden['nombre_usuario']) ?></p>
        <p class="mb-2"><strong>Fecha:</strong> <?= date('d/m/Y H:i', strtotime($orden['fecha_creacion'])) ?></p>
        <p class="mb-4"><strong>Estado:</strong> <?= htmlspecialchars($orden['estado_oc']) ?></p>

        <div class="mb-6">
            <h2 class="font-semibold text-gray-800">Descripción:</h2>
            <p class="text-gray-700"><?= nl2br(htmlspecialchars($orden['descripcion_oc'])) ?></p>
        </div>

        <?php if (!empty($solicitudesAsociadas)): ?>
            <div class="mb-6">
                <h2 class="font-semibold text-gray-800 mb-2">Solicitudes Asociadas:</h2>
                <ul class="list-disc ml-6">
                    <?php foreach ($solicitudesAsociadas as $sc): ?>
                        <li>
                            <a href="index.php?c=SolicitudCambio&a=detalle&id_sc=<?= $sc['id_sc'] ?>"
                               class="text-indigo-600 hover:underline">
                                <?= htmlspecialchars($sc['titulo']) ?> (SC#<?= $sc['id_sc'] ?>)
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <a href="index.php?c=OrdenCambio&a=listar" class="btn btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Volver
        </a>
    </div>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
