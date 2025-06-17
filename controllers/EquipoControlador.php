<?php
require_once __DIR__ . '/../model/EquipoModel.php';
require_once __DIR__ .  '/../model/ProyectoModel.php';
class EquipoControlador {
    private $equipoModel;
    private $proyectoModel;
    public function __construct() {
        $this->equipoModel = new EquipoModel();
        $this->proyectoModel = new ProyectoModel();
    }

    public function guardarEquipo() {
        $id_proyecto = $_POST['id_proyecto'] ?? null;
        $id_equipo = $_POST['id_equipo'] ?? null;
        $nombre_equipo = $_POST['nombre_equipo'] ?? null;

        if (empty($id_proyecto)) {
            echo "ID de proyecto faltante";
            return;
        }

        if ($id_equipo === 'nuevo' && !empty($nombre_equipo)) {
            // Crear nuevo equipo y asignarlo al proyecto
            $this->equipoModel->guardarNombreEquipo($id_proyecto, $nombre_equipo);
        } elseif (!empty($id_equipo)) {
            // Asignar equipo existente al proyecto
            $this->equipoModel->asignarEquipoExistenteAProyecto($id_proyecto, $id_equipo);
        } else {
            echo "Datos incompletos";
            return;
        }

        header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=$id_proyecto");
        exit;
    }

public function asignarMiembro() {
    $id_equipo = $_POST['id_equipo'] ?? null;
    $id_usuario = $_POST['id_usuario'] ?? null;
    $id_rol_proyecto = $_POST['id_rol_proyecto'] ?? null;
    $id_proyecto = $_POST['id_proyecto'] ?? null;

    if (!empty($id_equipo) && !empty($id_usuario) && !empty($id_rol_proyecto)) {
        $resultado = $this->equipoModel->asignarMiembroEquipo($id_equipo, $id_usuario, $id_rol_proyecto);

        // Prepara los datos necesarios para volver a cargar la vista
        $proyecto = $this->proyectoModel->obtenerProyectoPorId($id_proyecto);
        $equipo = $this->equipoModel->obtenerEquipoPorProyecto($id_proyecto);
        $usuarios = $this->equipoModel->obtenerUsuariosDisponibles();
        $roles_proyecto = $this->equipoModel->obtenerRolesProyecto();
        $miembros_equipo = $this->equipoModel->obtenerMiembrosEquipo($equipo['id_equipo']);

        $mensaje = $resultado
            ? ['tipo' => 'success', 'texto' => 'Miembro asignado exitosamente.']
            : ['tipo' => 'error', 'texto' => 'Este miembro ya tiene roles o ese rol ya fue asignado.'];

        require_once 'views/planificarProyectoVista.php'; // o la vista correspondiente
    } else {
        echo "Datos incompletos";
    }
}

    public function obtenerRoles() {
        $roles_proyecto  = $this->equipoModel->obtenerRolesProyecto();
        return ['status' => 'success', 'data' => $roles_proyecto ];
        require 'views/proyecto/gestionarEquipo.php';
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
    public function mostrar() {
    $idEquipo = $_GET['id_equipo'] ?? null;
    $id_proyecto = $_POST['id_proyecto'] ?? null;
    if ($idEquipo) {
        $equipo = $this->equipoModel->obtenerEquipoPorProyecto($id_proyecto);
        $miembros = $this->equipoModel->obtenerMiembrosEquipo($idEquipo);

        require_once __DIR__ . '/../views/planificarProyectoVista.php';
    } else {
        echo "ID de equipo no proporcionado.";
    }
}
public function modificarRol() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        $idMiembro = $_POST['id_miembro_equipo'] ?? null; 
        $idRolProyecto = $_POST['id_rol_proyecto'] ?? null;
        $idProyecto = $_POST['id_proyecto_redirect'] ?? null;

        if ($idMiembro && $idRolProyecto && $idProyecto) {
            $equipo = $this->equipoModel->obtenerEquipoPorProyecto($idProyecto);
            if (!$equipo) {
                echo "No se encontró el equipo para el proyecto.";
                return;
            }
            $idEquipo = $equipo['id_equipo']; 

            $actualizado = $this->equipoModel->actualizarRolMiembro($idMiembro, $idEquipo, $idRolProyecto);

            if ($actualizado) {
                header("Location: index.php?c=Proyecto&a=planificar&id_proyecto=" . $idProyecto);
                exit();
            } else {
                echo "No se pudo actualizar el rol del miembro.";
            }
        } else {
            echo "Faltan datos requeridos (miembro, rol o proyecto).";
        }
    }
}
public function eliminarMiembro() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idMiembro = $_POST['id_miembro'] ?? null;
        $idEquipo = $_POST['id_equipo'] ?? null;
        $idProyecto = $_POST['id_proyecto'] ?? null;

        if ($idMiembro && $idEquipo) {
            $resultado = $this->equipoModel->eliminarMiembroDeEquipo($idMiembro, $idEquipo); // Método nuevo

            if ($resultado) {
                header('Location: index.php?c=Proyecto&a=planificar&id_proyecto=' . $idProyecto);
                exit;
            } else {
                echo "No se pudo eliminar al miembro del equipo.";
            }
        } else {
            echo "ID de miembro o equipo no proporcionado.";
        }
    }
}

}