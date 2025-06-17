<?php include __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><title>Reporte PDF</title></head>
<body class="bg-gray-100">
  <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-4">Generar Reporte PDF</h1>
    <p>Pulsa el botón para descargar un informe completo de Solicitudes y Órdenes de Cambio.</p>
    <a href="index.php?c=Reporte&a=generarPDF"
       class="btn-primary mt-4 inline-block">
       📄 Descargar PDF
    </a>
  </div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
