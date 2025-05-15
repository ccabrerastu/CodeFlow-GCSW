<?php
require_once __DIR__ . '/../model/EquipoModel.php';

class EquipoControlador{
    private $equipoModel;

    public function __construct() {
        $this->equipoModel = new EquipoModel();
    }

    public function guardarNombreEquipo($id_proyecto, $nombre_equipo) {
        if (!empty($id_proyecto) && !empty($nombre_equipo)) {
            $this->equipoModel->guardarNombreEquipo($id_proyecto, $nombre_equipo);
            return ['status' => 'success', 'message' => 'Nombre del equipo guardado correctamente'];
        } else {
            return ['status' => 'error', 'message' => 'Datos incompletos'];
        }
    }

    public function asignarMiembro($id_equipo, $id_usuario, $id_rol_proyecto) {
        if (!empty($id_equipo) && !empty($id_usuario) && !empty($id_rol_proyecto)) {
            $this->equipoModel->asignarMiembroEquipo($id_equipo, $id_usuario, $id_rol_proyecto);
            return ['status' => 'success', 'message' => 'Miembro asignado con éxito'];
        } else {
            return ['status' => 'error', 'message' => 'Datos incompletos'];
        }
    }

    public function obtenerRoles() {
        $roles = $this->equipoModel->obtenerRolesProyecto();
        return ['status' => 'success', 'data' => $roles];
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
