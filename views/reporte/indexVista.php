<?php include __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte PDF</title>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-20 max-w-xl px-6">
        <div class="bg-white shadow-xl rounded-xl p-8 border border-gray-200">
            <div class="text-center">
                <h1 class="text-3xl font-extrabold text-gray-800 mb-3 flex items-center justify-center gap-2">
                    <i class="fas fa-file-pdf text-red-500"></i> Generar Reporte PDF
                </h1>
                <p class="text-gray-600 text-base">
                    Pulsa el botón para descargar un informe completo de
                    <span class="font-medium text-blue-600">Solicitudes</span> y
                    <span class="font-medium text-blue-600">Órdenes de Cambio</span>.
                </p>
                <a href="index.php?c=Reporte&a=generarPDF"
                   class="inline-block mt-6 bg-red-600 text-white font-semibold px-6 py-3 rounded-lg shadow hover:bg-red-700 transition">
                    📄 Descargar PDF
                </a>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
