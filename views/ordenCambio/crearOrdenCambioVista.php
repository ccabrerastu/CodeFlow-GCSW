<?php include __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>SGC – Generar Orden de Cambio</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow">

    <h1 class="text-2xl font-bold mb-4">Generar Orden de Cambio</h1>

    <form action="index.php?c=OrdenCambio&a=crear" method="POST" class="space-y-4">
      <input type="hidden" name="id_solicitud" value="<?= htmlspecialchars($formData['id_solicitud']) ?>">

      <div>
        <label class="block font-medium">Solicitud de Cambio #</label>
        <input type="text" readonly 
               value="<?= htmlspecialchars($formData['id_solicitud']) ?>"
               class="form-input bg-gray-200 w-full" />
      </div>

      <div>
        <label class="block font-medium">Descripción OC:</label>
        <textarea name="descripcion_detalle" rows="4" 
                  class="form-textarea w-full"
                  required><?= htmlspecialchars($formData['descripcion_detalle']) ?></textarea>
        <?php if (!empty($formErrors['descripcion_detalle'])): ?>
          <div class="text-red-600 text-sm"><?= htmlspecialchars($formErrors['descripcion_detalle']) ?></div>
        <?php endif; ?>
      </div>

      <div>
        <label class="block font-medium">Inicio ejecución planificado:</label>
        <input
          type="date"
          name="fecha_inicio_planificada"
          value="<?= htmlspecialchars($formData['fecha_inicio_planificada'] ?? '') ?>"
          class="form-input w-full"
          required
        />
        <?php if (!empty($formErrors['fecha_inicio_planificada'])): ?>
          <div class="text-red-600 text-sm"><?= htmlspecialchars($formErrors['fecha_inicio_planificada']) ?></div>
        <?php endif; ?>
      </div>

      <div>
        <label class="block font-medium">Fin ejecución planificado:</label>
        <input
          type="date"
          name="fecha_fin_ejecucion_planificada"
          value="<?= htmlspecialchars($formData['fecha_fin_ejecucion_planificada'] ?? '') ?>"
          class="form-input w-full"
          required
        />
        <?php if (!empty($formErrors['fecha_fin_ejecucion_planificada'])): ?>
          <div class="text-red-600 text-sm"><?= htmlspecialchars($formErrors['fecha_fin_ejecucion_planificada']) ?></div>
        <?php endif; ?>
      </div>

      <div>
        <label class="block font-medium">Ingeniero Responsable:</label>
        <select name="id_responsable" class="form-select w-full" required>
          <option value="">-- Seleccione --</option>
          <?php foreach($usuarios as $u): ?>
            <option value="<?= $u['id_usuario'] ?>"
              <?= ($formData['id_responsable']==$u['id_usuario'])?'selected':'' ?>>
              <?= htmlspecialchars($u['nombre_completo']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if (!empty($formErrors['id_responsable'])): ?>
          <div class="text-red-600 text-sm"><?= htmlspecialchars($formErrors['id_responsable']) ?></div>
        <?php endif; ?>
      </div>
      <button type="submit" class="btn-primary">Crear Orden de Cambio</button>
      <a href="index.php?c=OrdenCambio&a=index" class="ml-4 text-gray-600 hover:underline">Cancelar</a>
    </form>
  </div>
  <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
