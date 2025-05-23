<?php
require_once __DIR__ . '/../config/database.php';

class ElementoConfiguracionModel {
    private $id_ecs;
    private $nombre_ecs;
    private $descripcion;
    private $tipo_ecs;
    private $version_actual;
    private $estado_ecs;
    private $ruta_repositorio;

    private $conexion;

    public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en ElementoConfiguracionModel: " . $e->getMessage());
            die("Error de conexión a la base de datos. Por favor, contacte al administrador.");
        }
    }

    public function setIdEcs($id_ecs) { $this->id_ecs = $id_ecs; }
    public function getIdEcs() { return $this->id_ecs; }

    public function setNombreEcs($nombre_ecs) { $this->nombre_ecs = $nombre_ecs; }
    public function getNombreEcs() { return $this->nombre_ecs; }

    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function getDescripcion() { return $this->descripcion; }

    public function setTipoEcs($tipo_ecs) { $this->tipo_ecs = $tipo_ecs; }
    public function getTipoEcs() { return $this->tipo_ecs; }

    public function setVersionActual($version_actual) { $this->version_actual = $version_actual; }
    public function getVersionActual() { return $this->version_actual; }

    public function setEstadoEcs($estado_ecs) { $this->estado_ecs = $estado_ecs; }
    public function getEstadoEcs() { return $this->estado_ecs; }

    public function setRutaRepositorio($ruta_repositorio) { $this->ruta_repositorio = $ruta_repositorio; }
    public function getRutaRepositorio() { return $this->ruta_repositorio; }



    public function crearECS() {
        if ($this->conexion === null) {
            error_log("ElementoConfiguracionModel: No hay conexión a la base de datos.");
            return false;
        }

        if ($this->nombre_ecs === null) {
            error_log("ElementoConfiguracionModel::crearECS - Falta nombre_ecs.");
            return false;
        }

        $sql = "INSERT INTO ElementosConfiguracion 
                    (nombre_ecs, descripcion, tipo_ecs, version_actual, estado_ecs, ruta_repositorio) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (crearECS): " . $this->conexion->error);
            return false;
        }

        $descripcion_val = $this->descripcion ?? null;
        $tipo_ecs_val = $this->tipo_ecs ?? null;
        $version_val = $this->version_actual ?? '1.0';
        $estado_val = $this->estado_ecs ?? 'Definido';
        $ruta_repositorio_val = $this->ruta_repositorio ?? null;

        $stmt->bind_param("ssssss",
            $this->nombre_ecs,
            $descripcion_val,
            $tipo_ecs_val,
            $version_val,
            $estado_val,
            $ruta_repositorio_val
        );

        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $stmt->close();
            return $new_id;
        } else {
            error_log("Error al ejecutar la consulta (crearECS): " . $stmt->error . " SQL: " . $sql);
            $stmt->close();
            return false;
        }
    }


    public function obtenerTodosLosECS() {
        if ($this->conexion === null) return [];
        $sql = "SELECT * FROM ElementosConfiguracion ORDER BY nombre_ecs ASC";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerTodosLosECS: " . $this->conexion->error);
            return [];
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
        $ecs_lista = [];
        while ($fila = $resultado->fetch_assoc()) {
            $ecs_lista[] = $fila;
        }
        $stmt->close();
        return $ecs_lista;
    }


    public function obtenerECSPorId($id_ecs) {
        if ($this->conexion === null) return null;
        $sql = "SELECT * FROM ElementosConfiguracion WHERE id_ecs = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare obtenerECSPorId: " . $this->conexion->error);
            return null;
        }
        $stmt->bind_param("i", $id_ecs);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $ecs = $resultado->fetch_assoc();
        $stmt->close();
        return $ecs;
    }


    public function actualizarECS() {
        if ($this->conexion === null || $this->id_ecs === null) {
            error_log("ElementoConfiguracionModel: No hay conexión o ID de ECS no especificado.");
            return false;
        }
        
        $sql = "UPDATE ElementosConfiguracion SET 
                    nombre_ecs = ?, 
                    descripcion = ?, 
                    tipo_ecs = ?, 
                    version_actual = ?, 
                    estado_ecs = ?,
                    ruta_repositorio = ?,
                    fecha_ultima_modificacion = CURRENT_TIMESTAMP
                WHERE id_ecs = ?";
        $stmt = $this->conexion->prepare($sql);

        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (actualizarECS): " . $this->conexion->error);
            return false;
        }
        
        $stmt->bind_param("ssssssi",
            $this->nombre_ecs,
            $this->descripcion,
            $this->tipo_ecs,
            $this->version_actual,
            $this->estado_ecs,
            $this->ruta_repositorio,
            $this->id_ecs
        );

        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (actualizarECS): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }


    public function eliminarECS($id_ecs) {
        if ($this->conexion === null) return false;
        
        $sql = "DELETE FROM ElementosConfiguracion WHERE id_ecs = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare eliminarECS: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("i", $id_ecs);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error al ejecutar la consulta (eliminarECS): " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }

    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
?>
