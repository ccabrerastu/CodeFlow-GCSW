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
        $sql = "SELECT id_rol, nombre_rol FROM Roles";
        $resultado = $this->conexion->query($sql);
        $roles = [];
        while ($fila = $resultado->fetch_assoc()) {
            $roles[] = $fila;
        }
        return $roles;
    }

public function asignarMiembroEquipo($id_equipo, $id_usuario, $id_rol_proyecto) {
    // Verificar cuántos roles tiene ya el usuario en el equipo
    $stmt = $this->conexion->prepare("SELECT COUNT(*) as total FROM MiembrosEquipo WHERE id_equipo = ? AND id_usuario = ?");
    $stmt->bind_param("ii", $id_equipo, $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Verificar si ya tiene ese mismo rol
    $stmt = $this->conexion->prepare("SELECT COUNT(*) as existe FROM MiembrosEquipo WHERE id_equipo = ? AND id_usuario = ? AND id_rol_proyecto = ?");
    $stmt->bind_param("iii", $id_equipo, $id_usuario, $id_rol_proyecto);
    $stmt->execute();
    $verificaRol = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($resultado['total'] >= 1 || $verificaRol['existe'] > 0) {
        // Ya tiene 2 roles o el mismo rol ya fue asignado
        return false;
    }

    // Si pasa las verificaciones, insertar
    $stmt = $this->conexion->prepare("INSERT INTO MiembrosEquipo (id_equipo, id_usuario, id_rol_proyecto) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $id_equipo, $id_usuario, $id_rol_proyecto);
    $stmt->execute();
    $stmt->close();

    return true;
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
    public function obtenerEquipoPorProyecto2($id_proyecto) {
    $sql = "SELECT * FROM Equipos WHERE id_proyecto = ? LIMIT 1";
    $stmt = $this->conexion->prepare($sql);

    if (!$stmt) {
        die("Error al preparar la consulta: " . $this->db->error);
    }

    $stmt->bind_param("i", $id_proyecto); // "i" = integer
    $stmt->execute();

    $resultado = $stmt->get_result();
    $equipo = $resultado->fetch_assoc();

    $stmt->close();

    return $equipo;
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

   
}
