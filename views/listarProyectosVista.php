<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Gestión de Proyectos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            600: '#2563eb',
                            700: '#1d4ed8',
                        },
                        secondary: {
                            600: '#7c3aed',
                            700: '#6d28d9',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 font-sans antialiased">

<?php include __DIR__ . '/partials/header.php'; ?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <!-- Header con acciones -->
        <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Proyectos</h1>
                <p class="text-sm text-gray-500 mt-1">Listado completo de proyectos activos</p>
            </div>
            <a href="index.php?c=Proyecto&a=mostrarFormularioProyecto"
               class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg shadow hover:bg-primary-700 transition transform hover:-translate-y-0.5">
                <i class="fas fa-plus mr-2"></i> Nuevo Proyecto
            </a>
        </div>

        <!-- Mensaje de estado -->
        <?php if (isset($statusMessage) && $statusMessage): ?>
        <div class="mx-6 mt-4 p-4 rounded-lg text-sm font-medium flex items-start
            <?= $statusMessage['type'] === 'success' ? 
                'bg-green-50 text-green-800 border border-green-200' : 
                'bg-red-50 text-red-800 border border-red-200' ?>">
            <i class="fas <?= $statusMessage['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2 mt-0.5"></i>
            <span><?= htmlspecialchars($statusMessage['text']) ?></span>
        </div>
        <?php endif; ?>

        <!-- Tabla de proyectos -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metodología</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product Owner</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($proyectos)): ?>
                        <?php foreach ($proyectos as $proyecto): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($proyecto['id_proyecto']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($proyecto['nombre_proyecto']); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-500 break-words">
    <?= nl2br(htmlspecialchars($proyecto['descripcion'] ?? '')) ?>
</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <?= htmlspecialchars($proyecto['nombre_metodologia'] ?? 'N/A'); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div class="flex items-start space-x-2">
   <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 mr-2">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div><?= htmlspecialchars($proyecto['nombre_product_owner'] ?? 'N/A'); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    <?= $proyecto['estado_proyecto'] === 'Activo' ? 
                                        'bg-green-100 text-green-800' : 
                                        'bg-gray-100 text-gray-800' ?>">
                                    <i class="fas <?= $proyecto['estado_proyecto'] === 'Activo' ? 'fa-check-circle mr-1' : 'fa-pause-circle mr-1' ?>"></i>
                                    <?= htmlspecialchars($proyecto['estado_proyecto']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
    <div class="flex flex-col items-end space-y-2">
        <a href="index.php?c=Proyecto&a=planificar&id_proyecto=<?= $proyecto['id_proyecto'] ?>"
           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition">
            <i class="fas fa-calendar-alt mr-1 text-gray-500"></i> Planificar
        </a>
        <a href="index.php?c=Proyecto&a=mostrarFormularioProyecto&id_proyecto=<?= $proyecto['id_proyecto'] ?>"
           class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-secondary-600 hover:bg-secondary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-secondary-500 transition">
            <i class="fas fa-edit mr-1"></i> Editar
        </a>
    </div>
</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-folder-open text-3xl text-gray-300 mb-2"></i>
                                    <p class="text-sm">No hay proyectos registrados</p>
                                    <a href="index.php?c=Proyecto&a=mostrarFormularioProyecto" class="mt-2 text-sm text-primary-600 hover:text-primary-700">
                                        Crear primer proyecto
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>