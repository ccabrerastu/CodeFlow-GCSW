<?php
$esEditar      = !empty($formData['id_solicitud']);
$tituloPagina  = $esEditar ? 'Editar Solicitud de Cambio' : 'Nueva Solicitud de Cambio';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - <?= htmlspecialchars($tituloPagina) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container mx-auto mt-10 p-8 bg-white rounded-2xl shadow-2xl max-w-4xl">
    <h1 class="text-3xl font-extrabold text-gray-800 mb-6 border-b pb-2"><?= htmlspecialchars($tituloPagina) ?></h1>

    <?php if (!empty($formErrors['general'])): ?>
        <div class="p-4 mb-6 text-sm text-red-800 bg-red-100 border border-red-300 rounded-lg">
            <?= htmlspecialchars($formErrors['general']) ?>
        </div>
    <?php endif; ?>

    <form action="index.php?c=SolicitudCambio&a=<?= $esEditar ? 'editar' : 'crear' ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php if ($esEditar): ?>
            <input type="hidden" name="id_solicitud" value="<?= htmlspecialchars($formData['id_solicitud']) ?>">
        <?php endif; ?>

        <!-- Proyecto -->
       <div class="mb-4">
        <label for="id_proyecto" class="block text-sm font-medium text-gray-700 mb-1">
            <span class="inline-flex items-center gap-2">
                <i class="fas fa-diagram-project text-blue-500"></i> Proyecto:
            </span>
        </label>
        <div class="relative">
            <select name="id_proyecto" id="id_proyecto"
                    class="appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 pl-4 pr-10 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                    required>
                <option value="">-- Seleccione un proyecto --</option>
                <?php foreach ($proyectos as $p): ?>
                    <option value="<?= $p['id_proyecto'] ?>" <?= ($formData['id_proyecto'] == $p['id_proyecto']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nombre_proyecto']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>

        <?php if (!empty($formErrors['id_proyecto'])): ?>
            <p class="text-red-600 text-xs mt-2 flex items-center gap-1">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($formErrors['id_proyecto']) ?>
            </p>
        <?php endif; ?>
    </div>

        <!-- Prioridad -->
        <div class="mb-4">
            <label for="prioridad" class="block text-sm font-medium text-gray-700 mb-1">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-yellow-500"></i> Prioridad:
                </span>
            </label>
            <div class="relative">
                <select name="prioridad" id="prioridad"
                        class="appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 pl-4 pr-10 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        required>
                    <option value="">-- Seleccione prioridad --</option>
                    <?php foreach (['ALTA','MEDIA','BAJA'] as $p): ?>
                        <option value="<?= $p ?>" <?= (isset($formData['prioridad']) && $formData['prioridad'] === $p) ? 'selected' : '' ?>>
                            <?= ucfirst(strtolower($p)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>

            <?php if (!empty($formErrors['prioridad'])): ?>
                <p class="text-red-600 text-xs mt-2 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($formErrors['prioridad']) ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Tipo de Cambio -->
        <!-- Tipo de Cambio -->
        <div class="mb-4">
            <label for="tipo_cambio" class="block text-sm font-medium text-gray-700 mb-1">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-exchange-alt text-indigo-500"></i> Tipo de Cambio:
                </span>
            </label>
            <div class="relative">
                <select name="tipo_cambio" id="tipo_cambio"
                        class="appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 pl-4 pr-10 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                        required>
                    <option value="">-- Seleccionar tipo --</option>
                    <?php foreach(['CORRECCION','MEJORA','NUEVA_FUNCIONALIDAD'] as $tc): ?>
                        <option value="<?= $tc ?>" <?= (isset($formData['tipo_cambio']) && $formData['tipo_cambio']===$tc) ? 'selected':'' ?>>
                            <?= ucfirst(strtolower(str_replace('_',' ',$tc))) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>

            <?php if (!empty($formErrors['tipo_cambio'])): ?>
                <p class="text-red-600 text-xs mt-2 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($formErrors['tipo_cambio']) ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Impacto Estimado -->
        <div class="mb-4">
            <label for="impacto_estimado" class="block text-sm font-medium text-gray-700 mb-1">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-bullseye text-teal-500"></i> Impacto estimado:
                </span>
            </label>
            <input type="text" id="impacto_estimado" name="impacto_estimado" readonly
                class="w-full border border-gray-300 rounded-lg bg-gray-100 text-gray-700 py-2 px-4 shadow-sm"
                value="<?= htmlspecialchars($formData['impacto_estimado'] ?? '') ?>">
        </div>

        <!-- Justificación -->
        <div class="mb-4">
            <label for="justificacion" class="block text-sm font-medium text-gray-700 mb-1">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-comment-dots text-green-500"></i> Justificación:
                </span>
            </label>
            <textarea name="justificacion" id="justificacion" rows="3"
                    class="w-full border border-gray-300 rounded-lg shadow-sm text-gray-700 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($formData['justificacion'] ?? '') ?></textarea>

            <?php if (!empty($formErrors['justificacion'])): ?>
                <p class="text-red-600 text-xs mt-2 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($formErrors['justificacion']) ?>
                </p>
            <?php endif; ?>
        </div>

            <!-- Título -->
        <div class="mb-4">
            <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-heading text-blue-500"></i> Título:
                </span>
            </label>
            <input type="text"
                name="titulo"
                id="titulo"
                class="w-full border border-gray-300 rounded-lg shadow-sm py-2 px-4 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                value="<?= htmlspecialchars($formData['titulo'] ?? '') ?>"
                required>
            <?php if (!empty($formErrors['titulo'])): ?>
                <p class="text-red-600 text-xs mt-2 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($formErrors['titulo']) ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Descripción -->
        <div class="mb-4">
            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                <span class="inline-flex items-center gap-2">
                    <i class="fas fa-align-left text-indigo-500"></i> Descripción:
                </span>
            </label>
            <textarea name="descripcion"
                    id="descripcion"
                    rows="4"
                    class="w-full border border-gray-300 rounded-lg shadow-sm py-2 px-4 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                    required><?= htmlspecialchars($formData['descripcion'] ?? '') ?></textarea>
        </div>

        <!-- Archivos -->
        <div>
            <label class="block font-semibold text-gray-700 mb-1">Adjuntar justificantes:</label>
            <input type="file" name="archivos[]" multiple class="w-full border-gray-300 rounded-lg shadow-sm text-sm text-gray-600 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
        </div>

        <!-- Botones -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow">
                <?= $esEditar ? 'Actualizar' : 'Crear' ?>
            </button>
            <a href="index.php?c=SolicitudCambio&a=index" class="text-gray-600 hover:underline text-sm">
                Cancelar y volver
            </a>
        </div>
    </form>
</div>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <script>
    ;(function(){
        const selPrioridad = document.getElementById('prioridad');
        const selTipo      = document.getElementById('tipo_cambio');
        const outImpacto   = document.getElementById('impacto_estimado');

        const pesoPrioridad = { ALTA: 5, MEDIA: 3, BAJA: 1 };
        const pesoTipo      = { NUEVA_FUNCIONALIDAD: 4, MEJORA: 3, CORRECCION: 2 };

        function calcularImpacto() {
            const p = selPrioridad.value;
            const t = selTipo.value;
            if (!pesoPrioridad[p] || !pesoTipo[t]) {
                outImpacto.value = '';
                return;
            }
            const suma = pesoPrioridad[p] + pesoTipo[t];
            const max  = 5 + 4; 
            const pct  = Math.round(suma / max * 100);

            let label;
            if (pct <= 20)      label = 'Bajo';
            else if (pct <= 50) label = 'Moderado';
            else if (pct <= 80) label = 'Alto';
            else                label = 'Muy Alto';

            outImpacto.value = `${label} (${pct}%)`;
        }

        selPrioridad.addEventListener('change', calcularImpacto);
        selTipo.addEventListener('change', calcularImpacto);
        calcularImpacto();
    })();
    </script>
</body>
</html>
