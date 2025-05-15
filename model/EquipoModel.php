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
        $stmt = $this->conexion->prepare("SELECT id_equipo FROM Equipos WHERE id_proyecto = ?");
        $stmt->bind_param("i", $id_proyecto);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $row = $resultado->fetch_assoc();
            $id_equipo = $row['id_equipo'];
            $stmt->close();

            $stmtUpdate = $this->conexion->prepare("UPDATE Equipos SET nombre_equipo = ? WHERE id_equipo = ?");
            $stmtUpdate->bind_param("si", $nombre_equipo, $id_equipo);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        } else {
            $stmt->close();
            $stmtInsert = $this->conexion->prepare("INSERT INTO Equipos (id_proyecto, nombre_equipo) VALUES (?, ?)");
            $stmtInsert->bind_param("is", $id_proyecto, $nombre_equipo);
            $stmtInsert->execute();
            $stmtInsert->close();
        }
    }

    public function obtenerRolesProyecto() {
        $sql = "SELECT * FROM Roles";
        $resultado = $this->conexion->query($sql);
        $roles = [];
        while ($fila = $resultado->fetch_assoc()) {
            $roles[] = $fila;
        }
        return $roles;
    }

    public function asignarMiembroEquipo($id_equipo, $id_usuario, $id_rol_proyecto) {
        $stmt = $this->conexion->prepare("INSERT INTO MiembrosEquipo (id_equipo, id_usuario, id_rol_proyecto) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $id_equipo, $id_usuario, $id_rol_proyecto);
        $stmt->execute();
        $stmt->close();
    }

    public function obtenerEquipoPorProyecto($id_proyecto) {
        $stmt = $this->conexion->prepare("SELECT * FROM Equipos WHERE id_proyecto = ?");
        $stmt->bind_param("i", $id_proyecto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $equipo = $resultado->fetch_assoc();
        $stmt->close();
        return $equipo;
    }

    public function obtenerMiembrosEquipo($id_equipo) {
        $stmt = $this->conexion->prepare("SELECT u.nombre_completo, rp.nombre_rol
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

    public function obtenerProyectoPorEquipo($id_equipo) {
        $stmt = $this->conexion->prepare("SELECT id_proyecto FROM Equipos WHERE id_equipo = ?");
        $stmt->bind_param("i", $id_equipo);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $row = $resultado->fetch_assoc();
        $stmt->close();
        return $row ? $row['id_proyecto'] : null;
    }
}
