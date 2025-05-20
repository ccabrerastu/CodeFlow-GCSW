<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/ElementoConfiguracionModel.php'; // Necesario para el JOIN

class ECSFaseMetodologiaModel {
    private $conexion;

    public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
            if ($this->conexion === null) {
                throw new Exception("La conexión a la base de datos no se pudo establecer en ECSFaseMetodologiaModel.");
            }
        } catch (Exception $e) {
            error_log("Error de conexión en ECSFaseMetodologiaModel: " . $e->getMessage());
            throw new Exception("Error al inicializar ECSFaseMetodologiaModel: " . $e->getMessage());
        }
    }


    public function obtenerECSPorFase($id_fase_metodologia) {
        if ($this->conexion === null) {
             error_log("ECSFaseMetodologiaModel::obtenerECSPorFase - No hay conexión a la base de datos.");
             return [];
        }

        $sql = "SELECT 
                    efm.id_ec_fase_met, 
                    efm.id_fase_metodologia,
                    efm.id_ecs,
                    efm.descripcion as descripcion_en_fase,
                    ec.nombre_ecs,
                    ec.tipo_ecs,
                    ec.version_actual,
                    ec.estado_ecs
                FROM ECS_FaseMetodologia efm
                JOIN ElementosConfiguracion ec ON efm.id_ecs = ec.id_ecs
                WHERE efm.id_fase_metodologia = ?
                ORDER BY ec.nombre_ecs ASC";
        
        $stmt = $this->conexion->prepare($sql);
        if ($stmt === false) {
            error_log("Error en la preparación de la consulta (obtenerECSPorFase): " . $this->conexion->error);
            return [];
        }
        $stmt->bind_param("i", $id_fase_metodologia);
        if (!$stmt->execute()) {
            error_log("Error al ejecutar la consulta (obtenerECSPorFase): " . $stmt->error);
            $stmt->close();
            return [];
        }

        $resultado = $stmt->get_result();
        $ecs_fase = [];
        while ($fila = $resultado->fetch_assoc()) {
            $ecs_fase[] = $fila;
        }
        $stmt->close();
        return $ecs_fase;
    }


    public function asociarECSAFase($id_ecs, $id_fase_metodologia, $descripcion = null) {
        if ($this->conexion === null) return false;
        
        $sql = "INSERT INTO ECS_FaseMetodologia (id_ecs, id_fase_metodologia, descripcion) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error prepare asociarECSAFase: " . $this->conexion->error);
            return false;
            
        }

        $stmt->bind_param("iis", $id_ecs, $id_fase_metodologia, $descripcion);
        if ($stmt->execute()) {
            $new_id = $stmt->insert_id;
            $stmt->close();
            return $new_id;
        }
        error_log("Error execute asociarECSAFase: " . $stmt->error);
        $stmt->close();
        return false;
    }
    public function encontrarECSPersonalizado($id_ecs) {
        if ($this->conexion === null) return null;
        $sql = "SELECT * FROM ECS_FaseMetodologia WHERE id_ecs = ? AND id_fase_metodologia IS NULL LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error prepare encontrarECSPersonalizado: " . $this->conexion->error);
            return null;
        }
        $stmt->bind_param("i", $id_ecs);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $data = $resultado->fetch_assoc();
        $stmt->close();
        return $data;
    }

    public function __destruct() {
        if ($this->conexion) {
            
        }
    }
}
?>
