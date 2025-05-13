<?php
//include __DIR__ . '/../partials/header.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SGC - Registro de Nuevo Usuario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body { font-family: sans-serif; }
        .container { max-width: 600px; margin: 40px auto; padding: 30px; background-color: #fff; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .form-input { width: 100%; padding: 10px; margin-bottom: 5px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .form-label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .btn { padding: 10px 15px; border-radius: 4px; text-decoration: none; display: inline-block; font-size: 0.9rem; }
        .btn-primary { background-color: #28a745; color: white; border:none; } /* Verde para registrar */
        .btn-primary:hover { background-color: #218838; }
        .btn-secondary { background-color: #6c757d; color: white; border:none; }
        .btn-secondary:hover { background-color: #5a6268; }
        .error-message { color: #D0021B; font-size: 0.875em; margin-top: 2px; margin-bottom: 10px; }
        .form-title { font-size: 1.75rem; font-weight: bold; color: #333; text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="container">
        <h1 class="form-title">Registrar Nueva Cuenta</h1>

        <?php if (isset($formErrors['general'])): ?>
            <div class="p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                <?= htmlspecialchars($formErrors['general']) ?>
            </div>
        <?php endif; ?>

        <form action="index.php?c=Usuario&a=registrarUsuario" method="POST" class="space-y-4">
            <div>
                <label for="nombre_completo" class="form-label">Nombre Completo:</label>
                <input type="text" name="nombre_completo" id="nombre_completo" class="form-input"
                       value="<?= htmlspecialchars($formData['nombre_completo'] ?? '') ?>" required>
                <?php if (isset($formErrors['nombre_completo'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['nombre_completo']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="nombre_usuario" class="form-label">Nombre de Usuario:</label>
                <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-input"
                       value="<?= htmlspecialchars($formData['nombre_usuario'] ?? '') ?>" required>
                <?php if (isset($formErrors['nombre_usuario'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['nombre_usuario']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="email" class="form-label">Correo Electrónico:</label>
                <input type="email" name="email" id="email" class="form-input"
                       value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                <?php if (isset($formErrors['email'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['email']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="contrasena" class="form-label">Contraseña:</label>
                <input type="password" name="contrasena" id="contrasena" class="form-input" required>
                <?php if (isset($formErrors['contrasena'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['contrasena']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="confirmar_contrasena" class="form-label">Confirmar Contraseña:</label>
                <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" class="form-input" required>
                <?php if (isset($formErrors['confirmar_contrasena'])): ?>
                    <p class="error-message"><?= htmlspecialchars($formErrors['confirmar_contrasena']) ?></p>
                <?php endif; ?>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="index.php?c=Login&a=mostrarFormularioLogin" class="text-blue-600 hover:underline">&larr; Volver al Login</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-user-plus mr-1"></i> Registrarse
                </button>
            </div>
        </form>
    </div>
</body>
</html>
