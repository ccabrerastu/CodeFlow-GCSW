<?php
require_once __DIR__ . '/../config/database.php';
class EquipoModel {
    private $conexion;

     public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en EquipoModel: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }

    public function guardarNombreEquipo($id_proyecto, $nombre_equipo) {
        $stmt = $this->conexion->prepare("SELECT id_equipo FROM Proyectos WHERE id_proyecto = ?");
        $stmt->bind_param("i", $id_proyecto);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $id_equipo = $row['id_equipo'];
            $stmt->close();

            if (!empty($id_equipo)) {
                $stmtUpdate = $this->conexion->prepare("UPDATE Equipos SET nombre_equipo = ? WHERE id_equipo = ?");
                $stmtUpdate->bind_param("si", $nombre_equipo, $id_equipo);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            } else {
                $stmtInsert = $this->conexion->prepare("INSERT INTO Equipos (nombre_equipo) VALUES (?)");
                $stmtInsert->bind_param("s", $nombre_equipo);
                $stmtInsert->execute();
                $nuevo_id_equipo = $stmtInsert->insert_id;
                $stmtInsert->close();

                $stmtUpdateProyecto = $this->conexion->prepare("UPDATE Proyectos SET id_equipo = ? WHERE id_proyecto = ?");
                $stmtUpdateProyecto->bind_param("ii", $nuevo_id_equipo, $id_proyecto);
                $stmtUpdateProyecto->execute();
                $stmtUpdateProyecto->close();
            }
        } else {
            $stmt->close();
            echo "Proyecto no encontrado";
        }
    }


    public function obtenerRolesProyecto() {
        $sql = "SELECT id_rol, nombre_rol FROM Roles";
        $resultado = $this->conexion->query($sql);
        $roles = [];
        while ($fila = $resultado->fetch_assoc()) {
            $roles[] = $fila;
        }
        return $roles;
    }

    public function asignarMiembroEquipo($id_equipo, $id_usuario, $id_rol_proyecto) {
        $stmt = $this->conexion->prepare("SELECT COUNT(*) as total FROM MiembrosEquipo WHERE id_equipo = ? AND id_usuario = ?");
        $stmt->bind_param("ii", $id_equipo, $id_usuario);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $stmt = $this->conexion->prepare("SELECT COUNT(*) as existe FROM MiembrosEquipo WHERE id_equipo = ? AND id_usuario = ? AND id_rol_proyecto = ?");
        $stmt->bind_param("iii", $id_equipo, $id_usuario, $id_rol_proyecto);
        $stmt->execute();
        $verificaRol = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($resultado['total'] >= 1 || $verificaRol['existe'] > 0) {
            return false;
        }

        $stmt = $this->conexion->prepare("INSERT INTO MiembrosEquipo (id_equipo, id_usuario, id_rol_proyecto) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $id_equipo, $id_usuario, $id_rol_proyecto);
        $stmt->execute();
        $stmt->close();

        return true;
    }

    public function obtenerEquipoPorProyecto($id_proyecto) {
        $stmt = $this->conexion->prepare("SELECT id_equipo FROM Proyectos WHERE id_proyecto = ?");
        $stmt->bind_param("i", $id_proyecto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        
        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $id_equipo = $row['id_equipo'];
            $stmt->close();

            if (!empty($id_equipo)) {
                $stmtEquipo = $this->conexion->prepare("SELECT * FROM Equipos WHERE id_equipo = ?");
                $stmtEquipo->bind_param("i", $id_equipo);
                $stmtEquipo->execute();
                $resultadoEquipo = $stmtEquipo->get_result();
                $equipo = $resultadoEquipo->fetch_assoc();
                $stmtEquipo->close();

                return $equipo;
            } else {
                return null;
            }
        } else {
            $stmt->close();
            return null;
        }
    }


    public function obtenerEquipos() {
        $query = "SELECT id_equipo, nombre_equipo FROM Equipos";
        $result = $this->conexion->query($query);

        $equipos = [];
        while ($row = $result->fetch_assoc()) {
            $equipos[] = $row;
        }

        return $equipos;
    }


    public function obtenerMiembrosEquipo($id_equipo) {
        $stmt = $this->conexion->prepare("SELECT u.nombre_completo, rp.nombre_rol, me.id_rol_proyecto,  me.id_usuario
                                          FROM MiembrosEquipo me
                                          INNER JOIN Usuarios u ON me.id_usuario = u.id_usuario
                                          INNER JOIN Roles rp ON me.id_rol_proyecto = rp.id_rol
                                          WHERE me.id_equipo = ?");
        $stmt->bind_param("i", $id_equipo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $miembros = [];
        while ($fila = $resultado->fetch_assoc()) {
            $miembros[] = $fila;
        }

        $stmt->close();
        return $miembros;
    }

    public function obtenerProyectosPorEquipo($id_equipo) {
        $stmt = $this->conexion->prepare("SELECT * FROM Proyectos WHERE id_equipo = ?");
        $stmt->bind_param("i", $id_equipo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $proyectos = [];

        while ($fila = $resultado->fetch_assoc()) {
            $proyectos[] = $fila;
        }

        $stmt->close();
        return $proyectos;
    }

    public function obtenerEquipoPorProyecto2($id_proyecto) {
        $stmt = $this->conexion->prepare("SELECT id_equipo FROM Proyectos WHERE id_proyecto = ?");
        $stmt->bind_param("i", $id_proyecto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $proyecto = $resultado->fetch_assoc();
        $stmt->close();

        if (!$proyecto || empty($proyecto['id_equipo'])) {
            return null;
        }

        $id_equipo = $proyecto['id_equipo'];

        $stmt2 = $this->conexion->prepare("SELECT * FROM Equipos WHERE id_equipo = ?");
        $stmt2->bind_param("i", $id_equipo);
        $stmt2->execute();
        $resultado2 = $stmt2->get_result();
        $equipo = $resultado2->fetch_assoc();
        $stmt2->close();

        return $equipo ?: null;
    }

    public function obtenerUsuariosDisponibles() {
        $query = "SELECT id_usuario, nombre_completo AS nombre_completo FROM Usuarios";
        $result = $this->conexion->query($query);

        $usuarios = [];
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }

        return $usuarios;
    }

    public function obtenerMiembroPorIdYEquipo($id_usuario, $id_equipo) {
        $stmt = $this->conexion->prepare("SELECT * FROM MiembrosEquipo WHERE id_usuario = ? AND id_equipo = ?");
        $stmt->bind_param("ii", $id_usuario, $id_equipo);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function eliminarMiembroDeEquipo($id_usuario, $id_equipo) {
        $stmt = $this->conexion->prepare("DELETE FROM MiembrosEquipo WHERE id_usuario = ? AND id_equipo = ?");
        $stmt->bind_param("ii", $id_usuario, $id_equipo);
        return $stmt->execute();
    }
    public function actualizarRolMiembro($idMiembro, $idEquipo, $idRolProyecto) {
        $sql = "UPDATE MiembrosEquipo 
                SET id_rol_proyecto = ? 
                WHERE id_usuario = ? AND id_equipo = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return false;
        }
        $stmt->bind_param("iii", $idRolProyecto, $idMiembro, $idEquipo);
        $resultado = $stmt->execute();
        $stmt->close();
        return $resultado;
    }


    public function obtenerMiembrosParaSelect($id_proyecto) {
        if ($this->conexion === null) return [];

        $equipo = $this->obtenerEquipoPorProyecto2($id_proyecto);
        if (!$equipo || !isset($equipo['id_equipo'])) {
            return [];
        }

        $id_equipo = $equipo['id_equipo'];

        $sql = "SELECT u.id_usuario, u.nombre_completo 
                FROM MiembrosEquipo me
                INNER JOIN Usuarios u ON me.id_usuario = u.id_usuario
                WHERE me.id_equipo = ? AND u.activo = TRUE
                ORDER BY u.nombre_completo ASC";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerMiembrosParaSelect: " . $this->conexion->error);
            return [];
        }

        $stmt->bind_param("i", $id_equipo);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $miembros_select = [];
        while ($fila = $resultado->fetch_assoc()) {
            $miembros_select[] = $fila;
        }

        $stmt->close();
        return $miembros_select;
    }

    public function asignarEquipoExistenteAProyecto($id_proyecto, $id_equipo) {
        $stmt = $this->conexion->prepare("UPDATE Proyectos SET id_equipo = ? WHERE id_proyecto = ?");
        $stmt->bind_param("ii", $id_equipo, $id_proyecto);
        $stmt->execute();
        $stmt->close();
    }

}


