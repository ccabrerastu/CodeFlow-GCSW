<?php
// Variables esperadas del ProyectoControlador:
// $baseUrl (string)
// $proyecto (array, datos del proyecto actual para id_proyecto_redirect y nombre)
// $cronograma (array, datos del cronograma para id_cronograma)
// $fases_metodologia (array, lista de fases de la metodología del proyecto)
// $miembros_equipo (array, lista de miembros del equipo del proyecto para asignar responsables)
// $ecs_proyecto (array, lista de ECS definidos en el proyecto para seleccionar como entregable)
// $formData (array, datos para repoblar el formulario, será $actividad_data en edición o $formDataActividad en creación con errores)
// $formErrors (array, errores de validación del formulario)
// $accion_form (string, la acción del formulario, ej: "actualizarActividadCronograma" o "agregarActividadCronograma")
// $titulo_form (string, título para el formulario)
// $esEditar (boolean, true si es para editar, false para crear)

// Incluir el header común
include __DIR__ . '/../partials/header.php';

// Aseguramos que las variables esperadas existan para evitar errores de "undefined variable"
$id_proyecto_redirect = $proyecto['id_proyecto'] ?? null;
$id_cronograma_form = $cronograma['id_cronograma'] ?? null;

$nombre_actividad_val = $formData['nombre_actividad'] ?? '';
$descripcion_actividad_val = $formData['descripcion_actividad'] ?? '';
$id_fase_metodologia_val = $formData['id_fase_metodologia'] ?? null;
$fecha_inicio_planificada_val = $formData['fecha_inicio_planificada'] ?? '';
$fecha_fin_planificada_val = $formData['fecha_fin_planificada'] ?? '';
$fecha_entrega_real_val = $formData['fecha_entrega_real'] ?? '';
$estado_actividad_val = $formData['estado_actividad'] ?? 'Pendiente';
$id_responsable_val = $formData['id_responsable'] ?? null;
$id_ecs_entregable_val = $formData['id_ecs_entregable'] ?? null; // Este campo puede venir de EntregablesActividad

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - <?= htmlspecialchars($titulo_form ?? 'Gestionar Actividad') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #f9f9f9; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .btn { padding: 10px 15px; border-radius: 4px; text-decoration: none; display: inline-block; font-size: 0.9rem; }
        .btn-primary { background-color: #4A90E2; color: white; border:none; }
        .btn-primary:hover { background-color: #357ABD; }
        .btn-secondary { background-color: #6c757d; color: white; border:none; }
        .btn-secondary:hover { background-color: #5a6268; }
        .error-message { color: #D0021B; font-size: 0.875em; margin-top: 2px; margin-bottom: 10px; }
        .form-title { font-size: 1.75rem; font-weight: bold; color: #333; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body class="bg-gray-100">
    <?php include __DIR__ . '/../partials/header.php'; ?>

    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl">
        <h1 class="form-title"><?= htmlspecialchars($titulo_form ?? 'Gestionar Actividad') ?></h1>
        <p class="text-center text-gray-600 mb-6">Proyecto: <?= htmlspecialchars($proyecto['nombre_proyecto'] ?? 'Desconocido') ?></p>

        <?php if (isset($formErrors['general_actividad'])): ?>
            <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <?= htmlspecialchars($formErrors['general_actividad']) ?>
            </div>
        <?php endif; ?>

        <form action="<?= $baseUrl ?>index.php?c=Proyecto&a=<?= $accion_form ?>" method="POST" class="space-y-4">
            <?php if ($esEditar && isset($formData['id_actividad'])): ?>
                <input type="hidden" name="id_actividad" value="<?= htmlspecialchars($formData['id_actividad']) ?>">
            <?php endif; ?>
            <input type="hidden" name="id_proyecto_redirect" value="<?= htmlspecialchars($id_proyecto_redirect) ?>">
            <input type="hidden" name="id_cronograma" value="<?= htmlspecialchars($id_cronograma_form) ?>">

            <div>
                <label for="nombre_actividad" class="form-label">Nombre de la Actividad:</label>
                <input type="text" name="nombre_actividad" id="nombre_actividad" class="form-input" 
                       value="<?= htmlspecialchars($nombre_actividad_val) ?>" required>
                <?php if (isset($formErrors['nombre_actividad'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['nombre_actividad']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="descripcion_actividad" class="form-label">Descripción (Opcional):</label>
                <textarea name="descripcion_actividad" id="descripcion_actividad" rows="3" 
                          class="form-textarea"><?= htmlspecialchars($descripcion_actividad_val) ?></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="id_fase_metodologia" class="form-label">Fase de Metodología (Opcional):</label>
                    <select name="id_fase_metodologia" id="id_fase_metodologia" class="form-select">
                        <option value="">-- Ninguna --</option>
                        <?php if (!empty($fases_metodologia)): ?>
                            <?php foreach ($fases_metodologia as $fase_met): ?>
                                <option value="<?= htmlspecialchars($fase_met['id_fase_metodologia']) ?>"
                                    <?= ($id_fase_metodologia_val == $fase_met['id_fase_metodologia']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($fase_met['nombre_fase']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                             <option value="" disabled>No hay fases definidas para la metodología del proyecto.</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div>
                    <label for="id_responsable" class="form-label">Responsable (Opcional):</label>
                    <select name="id_responsable" id="id_responsable" class="form-select">
                        <option value="">-- Ninguno --</option>
                        <?php if (!empty($miembros_equipo)): ?>
                            <?php foreach ($miembros_equipo as $miembro): ?>
                                <option value="<?= htmlspecialchars($miembro['id_usuario']) ?>"
                                    <?= ($id_responsable_val == $miembro['id_usuario']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($miembro['nombre_completo']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No hay miembros en el equipo del proyecto.</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="fecha_inicio_planificada" class="form-label">Fecha Inicio Planificada:</label>
                    <input type="date" name="fecha_inicio_planificada" id="fecha_inicio_planificada" class="form-input"
                           value="<?= htmlspecialchars($fecha_inicio_planificada_val) ?>">
                    <?php if (isset($formErrors['fecha_inicio_planificada'])): ?>
                        <p class="error-message"><?= htmlspecialchars($formErrors['fecha_inicio_planificada']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="fecha_fin_planificada" class="form-label">Fecha Fin Planificada:</label>
                    <input type="date" name="fecha_fin_planificada" id="fecha_fin_planificada" class="form-input"
                           value="<?= htmlspecialchars($fecha_fin_planificada_val) ?>">
                    <?php if (isset($formErrors['fecha_fin_planificada'])): ?>
                        <p class="error-message"><?= htmlspecialchars($formErrors['fecha_fin_planificada']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 <div>
                    <label for="fecha_entrega_real" class="form-label">Fecha Entrega Real (Opcional):</label>
                    <input type="date" name="fecha_entrega_real" id="fecha_entrega_real" class="form-input"
                           value="<?= htmlspecialchars($fecha_entrega_real_val) ?>">
                </div>
                <div>
                    <label for="estado_actividad" class="form-label">Estado:</label>
                    <select name="estado_actividad" id="estado_actividad" class="form-select" required>
                        <option value="Pendiente" <?= ($estado_actividad_val == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                        <option value="En Progreso" <?= ($estado_actividad_val == 'En Progreso') ? 'selected' : '' ?>>En Progreso</option>
                        <option value="Completada" <?= ($estado_actividad_val == 'Completada') ? 'selected' : '' ?>>Completada</option>
                        <option value="Atrasada" <?= ($estado_actividad_val == 'Atrasada') ? 'selected' : '' ?>>Atrasada</option>
                        <option value="Bloqueada" <?= ($estado_actividad_val == 'Bloqueada') ? 'selected' : '' ?>>Bloqueada</option>
                    </select>
                     <?php if (isset($formErrors['estado_actividad'])): ?>
                        <p class="error-message"><?= htmlspecialchars($formErrors['estado_actividad']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-4">
                <label for="id_ecs_entregable" class="form-label">ECS Entregable Principal (Opcional):</label>
                <select name="id_ecs_entregable" id="id_ecs_entregable" class="form-select">
                    <option value="">-- Ninguno --</option>
                    <?php if (!empty($ecs_proyecto)): // Cambiado de $ecs_definidos a $ecs_proyecto para consistencia ?>
                        <?php foreach ($ecs_proyecto as $ecs_item): ?>
                            <option value="<?= htmlspecialchars($ecs_item['id_ecs']) ?>"
                                <?= ($id_ecs_entregable_val == $ecs_item['id_ecs']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ecs_item['nombre_ecs']) ?> (ID: <?= htmlspecialchars($ecs_item['id_ecs']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No hay ECS definidos para este proyecto.</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="flex items-center justify-end space-x-4 mt-6">
                <a href="index.php?c=Proyecto&a=planificar&id_proyecto=<?= htmlspecialchars($id_proyecto_redirect) ?>&tab=cronograma" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> <?= $esEditar ? 'Actualizar Actividad' : 'Crear Actividad' ?>
                </button>
            </div>
        </form>
    </div>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
