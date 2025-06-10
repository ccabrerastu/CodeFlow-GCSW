<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
    <h1 class="text-2xl font-bold mb-4">
        Orden #<?= htmlspecialchars($orden['id_orden'] ?? '') ?>
        – <?= htmlspecialchars($orden['titulo_solicitud'] ?? '') ?>
    </h1>

    <p class="mb-2"><strong>Creada por:</strong>
        <?= htmlspecialchars($orden['nombre_creador'] ?? '') ?></p>

    <p class="mb-2"><strong>Fecha:</strong>
        <?= !empty($orden['fecha_creacion'])
            ? date('d/m/Y H:i', strtotime($orden['fecha_creacion']))
            : '' ?></p>

    <p class="mb-4"><strong>Estado:</strong>
        <?= htmlspecialchars($orden['estado'] ?? '') ?></p>

    <h2 class="font-semibold mb-2">Descripción</h2>
    <p class="whitespace-pre-wrap"><?= nl2br(htmlspecialchars($orden['descripcion'] ?? '')) ?></p>

    <a href="index.php?c=OrdenCambio&a=index"
       class="mt-6 inline-block text-blue-600 hover:underline">
        ← Volver al listado
    </a>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>