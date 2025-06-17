<?php
    function cargarControlador($controlador) {
        $controlador = $controlador . "Controlador";
        $archivo = "controllers/" . $controlador . ".php";

    if (file_exists($archivo)) {
            require_once $archivo;

        
        if (class_exists($controlador)) {
                return new $controlador();
            } else {
            die("La clase '$controlador' no existe.");
            }
        } else {
            die("El archivo del controlador '$archivo' no fue encontrado.");
        }
    }

    function cargarAccion($controlador, $accion, $id = null) {
        if (! method_exists($controlador, $accion)) {
            die("La acción '$accion' no está definida en el controlador.");
        }

        if ($id !== null) {
            return $controlador->$accion($id);
        }

        if (isset($_GET['id_solicitud'])) {
            return $controlador->$accion((int)$_GET['id_solicitud']);
        }
        if (isset($_GET['id_orden'])) {
            return $controlador->$accion((int)$_GET['id_orden']);
        }
        if (isset($_GET['id_adjunto'])) {
            return $controlador->$accion((int)$_GET['id_adjunto']);
        }
        if (isset($_GET['id_sc'])) {
            return $controlador->$accion((int)$_GET['id_sc']);
        }
        if (isset($_GET['clave'])) {
            return $controlador->$accion($_GET['clave']);
        }
        if (get_class($controlador) === 'ReporteControlador' && $accion === 'generarPDF') {
            return $controlador->generarPDF();
        }

        return $controlador->$accion();
    }
?>