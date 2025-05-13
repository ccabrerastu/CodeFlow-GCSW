class UsuarioControlador {

    // Mostrar formulario de registro
    public function mostrarFormularioRegistro() {
        require __DIR__ . '/../views/registroVista.php'; // Cargar la vista de registro
    }

    // Registrar nuevo usuario
    public function registrar() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre_completo = $_POST['nombre_completo'];
            $nombre_usuario = $_POST['nombre_usuario'];
            $email = $_POST['email'];
            $clave = $_POST['clave'];

            // Validar que todos los campos estén llenos
            if (empty($nombre_completo) || empty($nombre_usuario) || empty($email) || empty($clave)) {
                $error_message = "Todos los campos son requeridos.";
                require __DIR__ . '/../views/registroVista.php';
                return;
            }

            // Crear un modelo de usuario y registrar el nuevo usuario
            $usuarioModel = new UsuarioModel();
            $exito = $usuarioModel->registrarUsuario($nombre_completo, $nombre_usuario, $email, $clave);

            if (!$exito) {
                // Si ya existe el usuario o el correo, mostrar error
                $error_message = "El nombre de usuario o el correo electrónico ya están registrados.";
                require __DIR__ . '/../views/registroVista.php';
                return;
            }

            // Si el registro fue exitoso, redirigir al login
            header("Location: index.php?c=Login&a=mostrarFormularioLogin");
            exit;
        } else {
            // Si no se ha enviado el formulario, redirigir al formulario de registro
            header("Location: index.php?c=Usuario&a=mostrarFormularioRegistro");
            exit;
        }
    }
}
