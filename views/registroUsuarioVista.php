<?php
// include __DIR__ . '/../partials/header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Registro de Usuario</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <!-- AOS (Animate on Scroll) -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-200 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white p-8 rounded-xl shadow-xl w-full max-w-xl" data-aos="zoom-in">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Registrar Nueva Cuenta</h1>

        <?php if (isset($formErrors['general'])): ?>
            <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 border border-red-300 rounded-md shadow-sm">
                <?= htmlspecialchars($formErrors['general']) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?c=Usuario&a=registrarUsuario" method="POST" class="space-y-5">
            <!-- Nombre completo -->
            <div>
                <label for="nombre_completo" class="block text-sm font-semibold text-gray-700">Nombre Completo</label>
                <input type="text" name="nombre_completo" id="nombre_completo" required
                    class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value="<?= htmlspecialchars($formData['nombre_completo'] ?? '') ?>">
                <?php if (isset($formErrors['nombre_completo'])): ?>
                    <p class="text-red-600 text-sm mt-1"><?= htmlspecialchars($formErrors['nombre_completo']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Usuario -->
            <div>
                <label for="nombre_usuario" class="block text-sm font-semibold text-gray-700">Nombre de Usuario</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario" required
                    class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value="<?= htmlspecialchars($formData['nombre_usuario'] ?? '') ?>">
                <?php if (isset($formErrors['nombre_usuario'])): ?>
                    <p class="text-red-600 text-sm mt-1"><?= htmlspecialchars($formErrors['nombre_usuario']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Correo -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700">Correo Electrónico</label>
                <input type="email" name="email" id="email" required
                    class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    value="<?= htmlspecialchars($formData['email'] ?? '') ?>">
                <?php if (isset($formErrors['email'])): ?>
                    <p class="text-red-600 text-sm mt-1"><?= htmlspecialchars($formErrors['email']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Contraseña -->
            <div>
                <label for="contrasena" class="block text-sm font-semibold text-gray-700">Contraseña</label>
                <input type="password" name="contrasena" id="contrasena" required
                    class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php if (isset($formErrors['contrasena'])): ?>
                    <p class="text-red-600 text-sm mt-1"><?= htmlspecialchars($formErrors['contrasena']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Confirmar contraseña -->
            <div>
                <label for="confirmar_contrasena" class="block text-sm font-semibold text-gray-700">Confirmar Contraseña</label>
                <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" required
                    class="w-full mt-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php if (isset($formErrors['confirmar_contrasena'])): ?>
                    <p class="text-red-600 text-sm mt-1"><?= htmlspecialchars($formErrors['confirmar_contrasena']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Botones -->
           <div class="flex items-center justify-between pt-2">
    <a href="index.php?c=Login&a=mostrarFormularioLogin"
       class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
        <i class="fas fa-arrow-left mr-1"></i> Volver al Login
    </a>
    <button type="submit"
        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg flex items-center gap-2">
        <i class="fas fa-user-plus"></i>
        Registrarse
    </button>
</div>
        </form>
    </div>

    <script>
        AOS.init({ once: true, duration: 900 });
    </script>
</body>
</html>
