<?php
require_once __DIR__ . '/../config/database.php';

class EntregableActividadModel {
    private $conexion;

    public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en EntregableActividadModel: " . $e->getMessage());
            die("Error de conexión a la base de datos.");
        }
    }

    public function asociarECSAActividad($id_actividad, $id_ecs, $fecha_entrega_planificada = null) {
        $sql = "INSERT INTO EntregablesActividad (id_actividad, id_ecs, fecha_entrega_planificada) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error prepare asociarECSAActividad: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("iis", $id_actividad, $id_ecs, $fecha_entrega_planificada);
        if ($stmt->execute()) {
            $stmt->close();
            return true;
        } else {
            error_log("Error execute asociarECSAActividad: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    public function desasociarECSDeActividad($id_actividad, $id_ecs) {
        $sql = "DELETE FROM EntregablesActividad WHERE id_actividad = ? AND id_ecs = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error prepare desasociarECSDeActividad: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("ii", $id_actividad, $id_ecs);
        $success = $stmt->execute();
        $stmt->close();
        return $success;
    }

    public function obtenerECSAsociadosAActividad($id_actividad) {
        if ($this->conexion === null) return [];
        $sql = "SELECT e.*, ea.fecha_entrega_planificada, ea.fecha_entrega_real 
                FROM ElementosConfiguracion e
                JOIN EntregablesActividad ea ON e.id_ecs = ea.id_ecs
                WHERE ea.id_actividad = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error prepare obtenerECSAsociadosAActividad: " . $this->conexion->error);
            return [];
        }
        $stmt->bind_param("i", $id_actividad);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $ecs_asociados = [];
        while ($fila = $resultado->fetch_assoc()) {
            $ecs_asociados[] = $fila;
        }
        $stmt->close();
        return $ecs_asociados;
    }

    public function registrarEntrega($id_actividad, $id_ecs, $ruta_archivo) {
        if ($this->conexion === null) return false;

        $fecha_entrega_real = date('Y-m-d H:i:s');

        $sql = "UPDATE EntregablesActividad 
                SET ruta_archivo = ?, fecha_entrega_real = ?
                WHERE id_actividad = ? AND id_ecs = ?";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error en prepare registrarEntrega: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("ssii", $ruta_archivo, $fecha_entrega_real, $id_actividad, $id_ecs);
        
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error execute registrarEntrega: " . $stmt->error);
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
