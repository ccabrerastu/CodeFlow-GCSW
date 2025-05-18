<?php
require_once __DIR__ . '/../config/database.php';

class FasesMetodologiaModel {
    private $id_fase_metodologia;
    private $id_metodologia;
    private $nombre_fase;
    private $descripcion;
    private $orden;

    private $conexion;

    public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en FasesMetodologiaModel: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }

    // --- Getters y Setters (puedes añadirlos según necesidad) ---
    public function setIdFaseMetodologia($id) { $this->id_fase_metodologia = $id; }
    public function getIdFaseMetodologia() { return $this->id_fase_metodologia; }
    public function setIdMetodologia($id) { $this->id_metodologia = $id; }
    public function getIdMetodologia() { return $this->id_metodologia; }
    public function setNombreFase($nombre) { $this->nombre_fase = $nombre; }
    public function getNombreFase() { return $this->nombre_fase; }
    public function setDescripcion($desc) { $this->descripcion = $desc; }
    public function getDescripcion() { return $this->descripcion; }
    public function setOrden($orden) { $this->orden = $orden; }
    public function getOrden() { return $this->orden; }



    public function obtenerFasesPorMetodologia($id_metodologia) {
        if ($this->conexion === null) {
            error_log("FasesMetodologiaModel: No hay conexión a la base de datos.");
            return [];
        }

        $sql = "SELECT id_fase_metodologia, nombre_fase, descripcion, orden 
                FROM FasesMetodologia 
                WHERE id_metodologia = ? 
                ORDER BY orden ASC, nombre_fase ASC";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (obtenerFasesPorMetodologia): " . $this->conexion->error);
            return [];
        }

        $stmt->bind_param("i", $id_metodologia);
        $stmt->execute();

        if ($stmt->error) {
            error_log("Error al ejecutar la consulta (obtenerFasesPorMetodologia): " . $stmt->error);
            $stmt->close();
            return [];
        }

        $resultado = $stmt->get_result();
        $fases = [];
        while ($fila = $resultado->fetch_assoc()) {
            $fases[] = $fila;
        }
        $stmt->close();
        return $fases;
    }


    public function crearFase() {
        if ($this->conexion === null) {
            error_log("FasesMetodologiaModel: No hay conexión a la base de datos.");
            return false;
        }
        $sql = "INSERT INTO FasesMetodologia (id_metodologia, nombre_fase, descripcion, orden) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (crearFase): " . $this->conexion->error);
            return false;
        }

        $stmt->bind_param("issi", $this->id_metodologia, $this->nombre_fase, $this->descripcion, $this->orden);

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $stmt->close();
            return $new_id;
        } else {
            error_log("Error al ejecutar la consulta (crearFase): " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    public function obtenerFasePorId($id_fase) {
        if ($this->conexion === null) {
            error_log("FasesMetodologiaModel: No hay conexión a la base de datos.");
            return null;
        }
        $sql = "SELECT id_fase_metodologia, id_metodologia, nombre_fase, descripcion, orden FROM FasesMetodologia WHERE id_fase_metodologia = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerFasePorId: " . $this->conexion->error);
            return null;
        }
        $stmt->bind_param("i", $id_fase);
        if (!$stmt->execute()) {
            error_log("Error en execute obtenerFasePorId: " . $stmt->error);
            $stmt->close();
            return null;
        }
        $resultado = $stmt->get_result();
        $fase = $resultado->fetch_assoc();
        $stmt->close();
        return $fase;
    }

    public function actualizarFase() {
        if ($this->conexion === null || $this->id_fase_metodologia === null) {
            error_log("FasesMetodologiaModel: No hay conexión o ID de fase no especificado para actualizar.");
            return false;
        }
        $sql = "UPDATE FasesMetodologia SET nombre_fase = ?, descripcion = ?, orden = ? WHERE id_fase_metodologia = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (actualizarFase): " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("ssii", $this->nombre_fase, $this->descripcion, $this->orden, $this->id_fase_metodologia);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (actualizarFase): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    public function eliminarFase($id_fase) {
        if ($this->conexion === null) {
            error_log("FasesMetodologiaModel: No hay conexión a la base de datos.");
            return false;
        }
        $sql = "DELETE FROM FasesMetodologia WHERE id_fase_metodologia = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (eliminarFase): " . $this->conexion->error);
            return false;
        }

        $stmt->bind_param("i", $id_fase);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (eliminarFase): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }
   public function obtenerFasesPorProyecto($id_proyecto) {
    if ($this->conexion === null) return [];

    $sql = "SELECT 
                f.id_fase_metodologia, 
                f.nombre_fase,
                e.id_ecs,
                e.nombre_ecs
            FROM Proyectos p
            JOIN FasesMetodologia f ON p.id_metodologia = f.id_metodologia
            LEFT JOIN ECS_FaseMetodologia efm ON f.id_fase_metodologia = efm.id_fase_metodologia
            LEFT JOIN ElementosConfiguracion e ON efm.id_ecs = e.id_ecs
            WHERE p.id_proyecto = ?
            ORDER BY f.id_fase_metodologia";

    $stmt = $this->conexion->prepare($sql);
    if (!$stmt) {
        error_log("Error en prepare obtenerFasesConElementosPorProyecto: " . $this->conexion->error);
        return [];
    }

    $stmt->bind_param("i", $id_proyecto);
    if (!$stmt->execute()) {
        error_log("Error en execute obtenerFasesConElementosPorProyecto: " . $stmt->error);
        return [];
    }

    $resultado = $stmt->get_result();

    $fases = [];
    while ($fila = $resultado->fetch_assoc()) {
        $id_fase = $fila['id_fase_metodologia'];

        if (!isset($fases[$id_fase])) {
            $fases[$id_fase] = [
                'id_fase_metodologia' => $id_fase,
                'nombre_fase' => $fila['nombre_fase'],
                'elementos' => []
            ];
        }

        if (!empty($fila['id_ecs'])) {
            $fases[$id_fase]['elementos'][] = [
                'id' => $fila['id_ecs'],
                'nombre' => $fila['nombre_ecs']
            ];
        }
    }

    $stmt->close();
    return array_values($fases);
}
    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
?>
