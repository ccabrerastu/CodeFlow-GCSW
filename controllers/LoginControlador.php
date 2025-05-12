<?php
require_once __DIR__ . '/../model/UsuarioModel.php';

class LoginControlador {

    public function index() {
        $this->mostrarFormularioLogin();
    }


    public function mostrarFormularioLogin() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $error_message = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        require __DIR__ . '/../views/loginVista.php';
    }


    public function autenticar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION['login_error']);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nombre_usuario = trim($_POST['nombre_usuario'] ?? '');
            $clave_plain = $_POST['clave'] ?? '';

            if (empty($nombre_usuario) || empty($clave_plain)) {
                $_SESSION['login_error'] = 'Nombre de usuario y contraseña son requeridos.';
                header("Location: index.php?c=Login&a=mostrarFormularioLogin");
                exit;
            }

            $usuarioModel = new UsuarioModel();
            $usuarioData = $usuarioModel->verificarCredenciales($nombre_usuario, $clave_plain);

            if ($usuarioData) {
                $_SESSION['id_usuario'] = $usuarioData['id_usuario'];
                $_SESSION['nombre_usuario'] = $usuarioData['nombre_usuario'];
                $_SESSION['nombre_completo'] = $usuarioData['nombre_completo'];

                header("Location: index.php?c=Metodologia&a=index");
                exit;
            } else {
                $_SESSION['login_error'] = 'Credenciales incorrectas o usuario inactivo.';
                header("Location: index.php?c=Login&a=mostrarFormularioLogin");
                exit;
            }
        } else {
            header("Location: index.php?c=Login&a=mostrarFormularioLogin");
            exit;
        }
    }

    public function cerrarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();

        header("Location: index.php?c=Login&a=mostrarFormularioLogin"); 
        exit;
    }
}
?>
