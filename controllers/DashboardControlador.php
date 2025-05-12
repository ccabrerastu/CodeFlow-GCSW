<?php
class DashboardControlador
{
    public function Vista()
    {
        session_start();

        // Verifica si hay una sesión iniciada
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php");
            exit();
        }

        // Redirige según el tipo de usuario
        if ($_SESSION['usuario'] === 'admin') {
            require 'views/DashboardA/Vista.php';
        } else {
            require 'views/DashboardU/Vista.php';
        }
    }
}
