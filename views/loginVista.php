<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Iniciar Sesión</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- AOS (Animate on Scroll) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-100 to-blue-200 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden w-full max-w-5xl flex flex-col md:flex-row transition" data-aos="fade-up">
        
        <!-- Lado izquierdo con imagen -->
        <div class="md:w-1/2 bg-gradient-to-br from-blue-600 to-indigo-700 text-white p-8 flex flex-col items-center justify-center" data-aos="fade-right">
            <img src="assets/imagenes/logo_sgc_placeholder.png" alt="Logo SGC" class="w-32 mb-4">
            <h2 class="text-2xl font-semibold mb-2 text-center">Sistema de Gestión de Cambios</h2>
            <img src="assets/imagenes/gestion_cambios_ilustracion.png" alt="Ilustración" class="w-full max-w-xs mt-4">
        </div>

        <!-- Formulario -->
        <form action="index.php?c=Login&a=autenticar" method="post" class="md:w-1/2 p-8 space-y-6 bg-white" data-aos="fade-left">
            <h1 class="text-3xl font-bold text-gray-800 text-center mb-4">Iniciar Sesión</h1>

            <div>
                <label for="nombre_usuario" class="block text-gray-700 font-semibold mb-1">Usuario</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       required>
            </div>

            <div>
                <label for="clave" class="block text-gray-700 font-semibold mb-1">Contraseña</label>
                <input type="password" name="clave" id="clave"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       required>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="text-red-600 font-semibold p-3 bg-red-100 border border-red-400 rounded shadow-sm">
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg flex items-center justify-center gap-2 transition">
                Ingresar
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </button>

            <div class="text-center text-sm mt-4">
                <a href="index.php?c=Usuario&a=mostrarFormularioRegistro" class="text-blue-600 hover:underline">
                    ¿No tienes cuenta? Regístrate
                </a>
            </div>
        </form>
    </div>

    <script>
        AOS.init({ once: true, duration: 900 });
    </script>
</body>
</html>
