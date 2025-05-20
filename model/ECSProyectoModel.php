<?php
require_once __DIR__ . '/../config/database.php';

class ECSProyectoModel {
    private $conexion;

    public function __construct() {
        try {
            $db_conexion = new Conexion();
            $this->conexion = $db_conexion->getConexion();
        } catch (Exception $e) {
            error_log("Error de conexión en ECSProyectoModel: " . $e->getMessage());
            die("Error de conexión a la base de datos.");
        }
    }


    public function obtenerIdsECSeleccionadosPorProyecto($id_proyecto) {
        if ($this->conexion === null) return [];

        $sql = "SELECT id_ec_fase_met FROM ECS_Proyecto WHERE id_proyecto = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error prepare obtenerIdsECSeleccionadosPorProyecto: " . $this->conexion->error);
            return [];
        }
        $stmt->bind_param("i", $id_proyecto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $ids_seleccionados = [];
        while ($fila = $resultado->fetch_assoc()) {
            $ids_seleccionados[] = $fila['id_ec_fase_met'];
        }
        $stmt->close();
        return $ids_seleccionados;
    }
    
    public function eliminarECSDelProyectoPorId($id_ecs_proyecto) {
        if ($this->conexion === null) return false;
        $sql = "DELETE FROM ECS_Proyecto WHERE id_ecs_proyecto = ?";
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error prepare eliminarECSDelProyectoPorId: " . $this->conexion->error);
            return false;
        }
        $stmt->bind_param("i", $id_ecs_proyecto);
        $success = $stmt->execute();
        if (!$success) {
            error_log("Error execute eliminarECSDelProyectoPorId: " . $stmt->error);
        }
        $stmt->close();
        return $success;
    }
    public function obtenerDetallesECSeleccionadosPorProyecto($id_proyecto) {
        if ($this->conexion === null) return [];

        $sql = "SELECT ep.id_ecs_proyecto, ep.estado, ep.comentario, ep.fecha_registro,
                       efm.id_ec_fase_met, efm.descripcion as descripcion_fase_ecs,
                       ec.id_ecs, ec.nombre_ecs, ec.tipo_ecs, ec.version_actual, ec.estado_ecs as estado_catalogo_ecs,
                       fm.nombre_fase
                FROM ECS_Proyecto ep
                JOIN ECS_FaseMetodologia efm ON ep.id_ec_fase_met = efm.id_ec_fase_met
                JOIN ElementosConfiguracion ec ON efm.id_ecs = ec.id_ecs
                JOIN FasesMetodologia fm ON efm.id_fase_metodologia = fm.id_fase_metodologia
                WHERE ep.id_proyecto = ?
                ORDER BY fm.orden ASC, ec.nombre_ecs ASC";
        
        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            error_log("Error prepare obtenerDetallesECSeleccionadosPorProyecto: " . $this->conexion->error);
            return [];
        }
        $stmt->bind_param("i", $id_proyecto);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $ecs_proyecto = [];
        while ($fila = $resultado->fetch_assoc()) {
            $ecs_proyecto[] = $fila;
        }
        $stmt->close();
        return $ecs_proyecto;
    }



    public function guardarSeleccionECS($id_proyecto, array $ids_ec_fase_met_seleccionados) {
        if ($this->conexion === null) return false;

        $this->conexion->begin_transaction();

        try {

            $actuales_stmt = $this->conexion->prepare("SELECT id_ec_fase_met FROM ECS_Proyecto WHERE id_proyecto = ?");
            $actuales_stmt->bind_param("i", $id_proyecto);
            $actuales_stmt->execute();
            $result_actuales = $actuales_stmt->get_result();
            $ids_actuales_en_db = [];
            while ($row = $result_actuales->fetch_assoc()) {
                $ids_actuales_en_db[] = $row['id_ec_fase_met'];
            }
            $actuales_stmt->close();

            $ids_a_eliminar = array_diff($ids_actuales_en_db, $ids_ec_fase_met_seleccionados);
            if (!empty($ids_a_eliminar)) {
                $placeholders_eliminar = implode(',', array_fill(0, count($ids_a_eliminar), '?'));
                $sql_delete = "DELETE FROM ECS_Proyecto WHERE id_proyecto = ? AND id_ec_fase_met IN ($placeholders_eliminar)";
                $stmt_delete = $this->conexion->prepare($sql_delete);
                $tipos_eliminar = "i" . str_repeat('i', count($ids_a_eliminar));
                $params_eliminar = array_merge([$id_proyecto], $ids_a_eliminar);
                $stmt_delete->bind_param($tipos_eliminar, ...$params_eliminar);
                if (!$stmt_delete->execute()) {
                    throw new Exception("Error al eliminar ECS no seleccionados: " . $stmt_delete->error);
                }
                $stmt_delete->close();
            }

            $ids_a_insertar = array_diff($ids_ec_fase_met_seleccionados, $ids_actuales_en_db);
            if (!empty($ids_a_insertar)) {
                $sql_insert = "INSERT INTO ECS_Proyecto (id_proyecto, id_ec_fase_met, estado) VALUES (?, ?, 'Definido')";
                $stmt_insert = $this->conexion->prepare($sql_insert);
                foreach ($ids_a_insertar as $id_ec_fase_met) {
                    $stmt_insert->bind_param("ii", $id_proyecto, $id_ec_fase_met);
                    if (!$stmt_insert->execute()) {
                        throw new Exception("Error al insertar nuevo ECS seleccionado: " . $stmt_insert->error);
                    }
                }
                $stmt_insert->close();
            }

            $this->conexion->commit();
            return true;

        } catch (Exception $e) {
            $this->conexion->rollback();
            error_log("Error en guardarSeleccionECS: " . $e->getMessage());
            return false;
        }
    }
    

    public function __destruct() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }
}
?>
