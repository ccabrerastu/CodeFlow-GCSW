<?php
require_once __DIR__ . '/../model/UsuarioModel.php';

class UsuarioControlador {

    private $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
    }

    public function mostrarFormularioRegistro() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $formData = $_SESSION['form_data_registro'] ?? [
            'nombre_completo' => '',
            'nombre_usuario' => '',
            'email' => '',
        ];
        $formErrors = $_SESSION['form_errors_registro'] ?? [];
        unset($_SESSION['form_data_registro'], $_SESSION['form_errors_registro']);

        $baseUrl = "/"; 
        require __DIR__ . '/../views/registroUsuarioVista.php';
    }


    public function registrarUsuario() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre_completo = trim($_POST['nombre_completo'] ?? '');
            $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';
            $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

            $formData = $_POST; 
            unset($formData['contrasena'], $formData['confirmar_contrasena']);

            $formErrors = [];

            // Validaciones
            if (empty($nombre_completo)) {
                $formErrors['nombre_completo'] = "El nombre completo es obligatorio.";
            }
            if (empty($nombre_usuario)) {
                $formErrors['nombre_usuario'] = "El nombre de usuario es obligatorio.";
            } elseif (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $nombre_usuario)) {
                $formErrors['nombre_usuario'] = "El nombre de usuario debe tener entre 4 y 20 caracteres alfanuméricos o guion bajo.";
            }

            if (empty($email)) {
                $formErrors['email'] = "El correo electrónico es obligatorio.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $formErrors['email'] = "El formato del correo electrónico no es válido.";
            }

            if (empty($contrasena)) {
                $formErrors['contrasena'] = "La contraseña es obligatoria.";
            } elseif (strlen($contrasena) < 6) {
                $formErrors['contrasena'] = "La contraseña debe tener al menos 6 caracteres.";
            }

            if ($contrasena !== $confirmar_contrasena) {
                $formErrors['confirmar_contrasena'] = "Las contraseñas no coinciden.";
            }

            // Verificar
            if (empty($formErrors)) {
                if ($this->usuarioModel->findByNombreUsuario($nombre_usuario)) {
                    $formErrors['nombre_usuario'] = "Este nombre de usuario ya está en uso.";
                }
                if ($this->usuarioModel->findByEmail($email)) {
                    $formErrors['email'] = "Este correo electrónico ya está registrado.";
                }
            }

            if (!empty($formErrors)) {
                $_SESSION['form_data_registro'] = $formData;
                $_SESSION['form_errors_registro'] = $formErrors;
                header("Location: index.php?c=Usuario&a=mostrarFormularioRegistro");
                exit;
            }

            $this->usuarioModel->setNombreCompleto($nombre_completo);
            $this->usuarioModel->setNombreUsuario($nombre_usuario);
            $this->usuarioModel->setEmail($email);
            $this->usuarioModel->setContrasena($contrasena);
            $this->usuarioModel->setActivo(true);

            $nuevoUsuarioId = $this->usuarioModel->agregarUsuario();

            if ($nuevoUsuarioId) {
                $_SESSION['status_message'] = ['type' => 'success', 'text' => '¡Usuario registrado exitosamente! Ahora puedes iniciar sesión.'];
                header("Location: index.php?c=Login&a=mostrarFormularioLogin");
                exit;
            } else {
                $_SESSION['form_data_registro'] = $formData;
                $_SESSION['form_errors_registro'] = ['general' => 'Error inesperado al registrar el usuario. Inténtelo de nuevo.'];
                header("Location: index.php?c=Usuario&a=mostrarFormularioRegistro");
                exit;
            }
        } else {
            header("Location: index.php?c=Usuario&a=mostrarFormularioRegistro");
            exit;
        }
    }

}
?>
