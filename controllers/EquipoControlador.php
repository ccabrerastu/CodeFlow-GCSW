<?php
require_once __DIR__ . '/../model/EquipoModel.php';

class EquipoControlador {
    private $equipoModel;

    public function __construct() {
        $this->equipoModel = new EquipoModel();
    }

    public function guardarEquipo() {
        $id_proyecto = $_POST['id_proyecto'] ?? null;
        $nombre_equipo = $_POST['nombre_equipo'] ?? null;

        if (!empty($id_proyecto) && !empty($nombre_equipo)) {
            $this->equipoModel->guardarNombreEquipo($id_proyecto, $nombre_equipo);
            // Redirigir o retornar JSON según necesidad
            header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=$id_proyecto");
            exit;
        } else {
            echo "Datos incompletos";
        }
    }

    public function asignarMiembro() {
        $id_equipo = $_POST['id_equipo'] ?? null;
        $id_usuario = $_POST['id_usuario'] ?? null;
        $id_rol_proyecto = $_POST['id_rol_proyecto'] ?? null;
        $id_proyecto = $_POST['id_proyecto'] ?? null; // Por si necesitas redirigir

        if (!empty($id_equipo) && !empty($id_usuario) && !empty($id_rol_proyecto)) {
            $this->equipoModel->asignarMiembroEquipo($id_equipo, $id_usuario, $id_rol_proyecto);
            header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=$id_proyecto");
            exit;
        } else {
            echo "Datos incompletos";
        }
    }

    public function obtenerRoles() {
        $roles = $this->equipoModel->obtenerRolesProyecto();
        return ['status' => 'success', 'data' => $roles];
        require 'views/planificarProyectosVista.php';
    }

    public function obtenerEquipoPorProyecto($id_proyecto) {
        if (!empty($id_proyecto)) {
            $equipo = $this->equipoModel->obtenerEquipoPorProyecto($id_proyecto);
            return ['status' => 'success', 'data' => $equipo];
        }
        return ['status' => 'error', 'message' => 'ID de proyecto requerido'];
    }

    public function obtenerMiembrosEquipo($id_equipo) {
        if (!empty($id_equipo)) {
            $miembros = $this->equipoModel->obtenerMiembrosEquipo($id_equipo);
            return ['status' => 'success', 'data' => $miembros];
        }
        return ['status' => 'error', 'message' => 'ID de equipo requerido'];
    }

    public function obtenerProyectoPorEquipo($id_equipo) {
        if (!empty($id_equipo)) {
            $proyecto = $this->equipoModel->obtenerProyectoPorEquipo($id_equipo);
            return ['status' => 'success', 'data' => $proyecto];
        }
        return ['status' => 'error', 'message' => 'ID de equipo requerido'];
    }
}
