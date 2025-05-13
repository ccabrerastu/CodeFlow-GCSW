<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Registro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-200 flex items-center justify-center min-h-screen">

    <div class="bg-gray-100 p-8 rounded-lg shadow-lg flex gap-8 w-full max-w-4xl">

        <div class="flex flex-col items-center justify-center w-1/2">
            <img src="assets/imagenes/logo_sgc_placeholder.png" alt="Logo SGC" class="w-40 mb-6">
            <img src="assets/imagenes/gestion_cambios_ilustracion.png" alt="Ilustración Gestión de Cambios" class="w-full max-w-xs">
        </div>

        <form action="index.php?c=Usuario&a=registrar" method="POST" class="w-1/2 space-y-4">
            <h1 class="text-3xl font-bold text-center mb-6 drop-shadow">SGC - Registro de Usuario</h1>

            <div>
                <label for="nombre_completo" class="block mb-1 font-semibold">Nombre Completo:</label>
                <input type="text" name="nombre_completo" id="nombre_completo" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <div>
                <label for="nombre_usuario" class="block mb-1 font-semibold">Nombre de Usuario:</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <div>
                <label for="email" class="block mb-1 font-semibold">Correo Electrónico:</label>
                <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <div>
                <label for="clave" class="block mb-1 font-semibold">Contraseña:</label>
                <input type="password" name="clave" id="clave" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400" required>
            </div>

            <?php if (!empty($error_message)): ?>
                <div class="text-red-600 font-semibold p-2 bg-red-100 border border-red-400 rounded"><?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>

            <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 rounded hover:bg-blue-700 transition flex items-center justify-center gap-2">
                Registrarse
            </button>

            <div class="text-center mt-4">
                <a href="index.php?c=Login&a=mostrarFormularioLogin" class="text-blue-600 hover:underline">¿Ya tienes cuenta? Inicia sesión</a>
            </div>
        </form>
    </div>

</body>
</html>
